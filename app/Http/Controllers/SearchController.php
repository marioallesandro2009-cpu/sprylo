<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $products = Product::with('images')
            ->where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%')
                    ->orWhereHas('brand', fn ($q) => $q->where('name', 'like', '%'.$term.'%'))
                    ->orWhereHas('category', fn ($q) => $q->where('name', 'like', '%'.$term.'%'));
            })
            ->orderByRaw(
                'CASE
                    WHEN products.name LIKE ? THEN 0
                    WHEN products.name LIKE ? THEN 1
                    WHEN EXISTS (SELECT 1 FROM brands WHERE brands.id = products.brand_id AND brands.name LIKE ?) THEN 2
                    WHEN EXISTS (SELECT 1 FROM categories WHERE categories.id = products.category_id AND categories.name LIKE ?) THEN 3
                    ELSE 4
                END',
                [$term.'%', '%'.$term.'%', '%'.$term.'%', '%'.$term.'%']
            )
            ->orderBy('products.name')
            ->take(6)
            ->get();

        $results = $products->map(fn (Product $product) => [
            'name' => $product->name,
            'url' => route('product.show', $product),
            'image' => $product->images->first()->image ?? null,
            'price' => rupiah($product->sale_price ?? $product->price),
            'original_price' => $product->sale_price ? rupiah($product->price) : null,
        ]);

        return response()->json(['results' => $results]);
    }
}