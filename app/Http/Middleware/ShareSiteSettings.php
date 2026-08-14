<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\FooterLink;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareSiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        View::share('settings', Setting::pluck('value', 'key'));
        View::share('categories', Category::orderBy('name')->take(6)->get());
        View::share('cartCount', array_sum(session('cart', [])));
        View::share('wishlistIds', session('wishlist', []));
        View::share('wishlistCount', count(session('wishlist', [])));
        View::share('footerLinks', FooterLink::orderBy('sort_order')->get()->groupBy('group'));

        return $next($request);
    }
}
