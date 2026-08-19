<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show(string $product)
    {
        $product = Product::withRatingStats()
            ->with(['images', 'category', 'brand'])
            ->where('slug', $product)
            ->firstOrFail();

        $specs = [
            ['label' => 'Category', 'value' => $product->category->name],
            ['label' => 'Brand', 'value' => $product->brand->name ?? '—'],
            ['label' => 'Price', 'value' => rupiah($product->price)],
            ['label' => 'Stock', 'value' => $product->stock.' units'],
            ['label' => 'Weight', 'value' => weight_label($product->weight)],
            ['label' => 'Rating', 'value' => $product->reviews_count > 0
                ? $product->rating_avg.' / 5 ('.$product->reviews_count.' reviews)'
                : 'No reviews yet'],
            ['label' => 'SKU', 'value' => strtoupper($product->slug)],
        ];

        $reviews = $product->reviews;

        $breakdown = [];
        $ratingCounts = $product->reviews()->select('rating', DB::raw('count(*) as total'))->groupBy('rating')->pluck('total', 'rating');
        for ($stars = 5; $stars >= 1; $stars--) {
            $count = $ratingCounts[$stars] ?? 0;
            $breakdown[$stars] = $product->reviews_count > 0 ? round(($count / $product->reviews_count) * 100) : 0;
        }

        $reviewSummary = [
            'average' => $product->rating_avg,
            'count' => $product->reviews_count,
            'breakdown' => $breakdown,
        ];

        $relatedProducts = Product::withRatingStats()
            ->with('images')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(5)
            ->get();

        return view('pages.product', compact('product', 'specs', 'reviewSummary', 'reviews', 'relatedProducts'));
    }
}
