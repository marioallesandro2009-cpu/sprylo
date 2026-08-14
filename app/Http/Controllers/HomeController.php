<?php

namespace App\Http\Controllers;

use App\Models\HeroHighlight;
use App\Models\PageMeta;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $pageMeta = PageMeta::forPage('home');

        $heroHighlights = HeroHighlight::with('product')->get()->keyBy('position');

        $trendingProducts = Product::withRatingStats()
            ->with('images')
            ->where('is_active', true)
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        $discountBanners = Product::withRatingStats()
            ->with('images')
            ->where('is_active', true)
            ->whereNotNull('sale_price')
            ->take(2)
            ->get();

        $justForYou = Product::withRatingStats()
            ->with('images')
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('pages.home', compact(
            'pageMeta', 'heroHighlights', 'trendingProducts', 'discountBanners', 'justForYou'
        ));
    }
}
