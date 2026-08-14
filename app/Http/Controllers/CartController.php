<?php

namespace App\Http\Controllers;

use App\Models\PageMeta;
use App\Models\Product;
use App\Models\PromoCode;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('cart');

        $cartItems = Cart::items();
        $totals = Cart::totals($cartItems);

        return view('pages.cart', compact('pageMeta', 'cartItems', 'totals'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        if ($product->stock < 1) {
            return back()->with('error', 'This product is out of stock.');
        }

        $qty = max(1, (int) $request->input('qty', 1));

        $cart = session('cart', []);
        $currentQty = $cart[$product->id] ?? 0;
        $cart[$product->id] = min($currentQty + $qty, $product->stock);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', $product->name.' added to cart.');
    }

    public function update(Request $request): RedirectResponse
    {
        $cart = session('cart', []);

        foreach ($request->input('qty', []) as $productId => $qty) {
            $qty = (int) $qty;

            if ($qty <= 0 || ! isset($cart[$productId])) {
                unset($cart[$productId]);

                continue;
            }

            $product = Product::find($productId);
            $cart[$productId] = $product ? min($qty, max($product->stock, 1)) : $qty;
        }

        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function applyPromo(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $code = strtoupper(trim($request->input('code')));
        $subtotal = (float) Cart::items()->sum('subtotal');

        $promo = PromoCode::where('code', $code)->active()->first();

        if (! $promo || ! $promo->appliesTo($subtotal)) {
            return redirect()->route('cart.index')->with('error', 'That promo code isn\'t valid for this order.');
        }

        session(['promo_code' => $promo->code]);

        return redirect()->route('cart.index')->with('success', 'Promo code "'.$promo->code.'" applied.');
    }

    public function removePromo(): RedirectResponse
    {
        session()->forget('promo_code');

        return redirect()->route('cart.index')->with('success', 'Promo code removed.');
    }
}
