<?php

namespace App\Http\Controllers;

use App\Models\PageMeta;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('wishlist');

        $ids = session('wishlist', []);
        $products = Product::withRatingStats()
            ->with('images')
            ->whereIn('id', $ids)
            ->get();

        return view('pages.wishlist', compact('pageMeta', 'products'));
    }

    public function add(Product $product): RedirectResponse
    {
        $wishlist = session('wishlist', []);

        if (! in_array($product->id, $wishlist)) {
            $wishlist[] = $product->id;
        }

        session(['wishlist' => $wishlist]);

        return back()->with('success', $product->name.' added to your wishlist.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $wishlist = array_values(array_diff(session('wishlist', []), [$product->id]));

        session(['wishlist' => $wishlist]);

        return back()->with('success', $product->name.' removed from your wishlist.');
    }
}
