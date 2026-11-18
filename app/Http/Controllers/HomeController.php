<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use App\Models\PageMeta;
use App\Models\Product;
use App\Models\PromoBanner;

class HomeController extends Controller
{
    public function index()
    {
        $pageMeta = PageMeta::forPage('home');

        $heroSlides = HeroSlide::with('product')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $trendingProducts = Product::withRatingStats()
            ->with('images')
            ->where('is_active', true)
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        $promoBanners = PromoBanner::with(['product.images'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $justForYou = Product::withRatingStats()
            ->with('images')
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $trendingTabs = \App\Models\Category::query()
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->whereHas('products', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('products_count')
            ->take(4)
            ->get()
            ->map(function (\App\Models\Category $category) {
                $category->setRelation('trending', Product::withRatingStats()
                    ->with('images')
                    ->where('is_active', true)
                    ->where('category_id', $category->id)
                    ->orderByDesc('reviews_count')
                    ->take(5)
                    ->get());

                return $category;
            })
            ->values();

        return view('pages.home', compact(
            'pageMeta', 'heroSlides', 'trendingProducts', 'trendingTabs', 'promoBanners', 'justForYou'
        ));
    }
}
