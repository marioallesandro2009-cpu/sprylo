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

        return view('pages.home', compact(
            'pageMeta', 'heroSlides', 'trendingProducts', 'promoBanners', 'justForYou'
        ));
    }
}
