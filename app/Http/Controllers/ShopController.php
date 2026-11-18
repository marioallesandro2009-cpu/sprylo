<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PageMeta;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $pageMeta = PageMeta::forPage('shop');

        $query = Product::withRatingStats()
            ->with(['images', 'category', 'brand'])
            ->where('is_active', true);

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($sub) use ($term) {
                $sub->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%')
                    ->orWhereHas('brand', fn ($q) => $q->where('name', 'like', '%'.$term.'%'))
                    ->orWhereHas('category', fn ($q) => $q->where('name', 'like', '%'.$term.'%'));
            });
        }
        if ($request->filled('categories')) {
            $query->whereIn('category_id', (array) $request->categories);
        }
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', (array) $request->brands);
        }
        if ($request->filled('price_min')) {
            $query->whereRaw('LEAST(COALESCE(sale_price, price), COALESCE(promo_price, price)) >= ?', [$request->price_min]);
        }
        if ($request->filled('price_max')) {
            $query->whereRaw('LEAST(COALESCE(sale_price, price), COALESCE(promo_price, price)) <= ?', [$request->price_max]);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if ($request->boolean('on_sale')) {
            $query->where(fn ($q) => $q->whereNotNull('sale_price')->orWhereNotNull('promo_price'));
        }
        if ($request->boolean('new_arrivals')) {
            $query->where('created_at', '>=', now()->subDays(14));
        }

        match ($request->get('sort')) {
            'price_asc' => $query->orderByRaw('LEAST(COALESCE(sale_price, price), COALESCE(promo_price, price)) asc'),
            'price_desc' => $query->orderByRaw('LEAST(COALESCE(sale_price, price), COALESCE(promo_price, price)) desc'),
            'newest' => $query->latest(),
            'rating' => $query->orderByDesc('rating_avg'),
            default => $query->orderByDesc('reviews_count'),
        };

        $products = $query->get();

        if ($request->filled('min_rating')) {
            $products = $products->filter(fn ($product) => $product->rating_avg >= (float) $request->min_rating)->values();
        }

        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])->orderBy('name')->get();
        $brands = Brand::withCount(['products' => fn ($q) => $q->where('is_active', true)])->orderBy('sort_order')->get();

        $activeProducts = Product::withRatingStats()->where('is_active', true)->get();
        $priceRange = [
            'min' => (float) $activeProducts->min(fn ($p) => $p->sale_price ?? $p->price) ?: 0,
            'max' => (float) $activeProducts->max(fn ($p) => $p->sale_price ?? $p->price) ?: 0,
        ];
        $availabilityCounts = [
            'in_stock' => $activeProducts->where('stock', '>', 0)->count(),
            'on_sale' => $activeProducts->whereNotNull('sale_price')->count(),
            'new_arrivals' => $activeProducts->filter(fn ($p) => $p->created_at->gt(now()->subDays(14)))->count(),
        ];
        $ratingCounts = collect([5, 4, 3])->mapWithKeys(fn ($stars) => [
            $stars => $activeProducts->filter(fn ($p) => $p->rating_avg >= $stars)->count(),
        ]);

        return view('pages.shop', compact(
            'pageMeta', 'products', 'categories', 'brands', 'priceRange', 'availabilityCounts', 'ratingCounts'
        ));
    }
}
