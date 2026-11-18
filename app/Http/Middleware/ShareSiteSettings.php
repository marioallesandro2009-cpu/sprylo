<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\FooterLink;
use App\Models\NavLink;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareSiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = Setting::pluck('value', 'key');

        View::share('settings', $settings);
        // $ui('ui_key', ['count' => 5]) returns an editable storefront text with {placeholders} filled in.
        View::share('ui', function (string $key, array $replace = []) use ($settings): string {
            $text = (string) ($settings[$key] ?? '');

            foreach ($replace as $name => $value) {
                $text = str_replace('{'.$name.'}', (string) $value, $text);
            }

            return $text;
        });
        View::share('navLinks', NavLink::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get());
        View::share('categories', Category::orderBy('name')->take(6)->get());
        View::share('cartCount', array_sum(session('cart', [])));
        View::share('wishlistIds', session('wishlist', []));
        View::share('wishlistCount', count(session('wishlist', [])));
        View::share('compareIds', session('compare', []));
        View::share('footerLinks', FooterLink::orderBy('sort_order')->get()->groupBy('group'));

        return $next($request);
    }
}
