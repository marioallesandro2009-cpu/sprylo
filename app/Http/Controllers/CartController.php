<?php

namespace App\Http\Controllers;

use App\Models\PageMeta;
use App\Models\Product;
use App\Models\PromoCode;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
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
        $variantId = $request->integer('variant_id') ?: null;
        $variant = $variantId ? $product->variants()->find($variantId) : null;

        if ($variantId && ! $variant) {
            abort(404);
        }

        // The option is picked at checkout, so a variant product is added with its first
        // in-stock variant as a placeholder the shopper can change there.
        if (! $variant && $product->variants()->exists()) {
            $variant = $product->variants()->where('stock', '>', 0)->first();
            $variantId = $variant?->id;

            if (! $variant) {
                return back()->with('error', 'This product is out of stock.');
            }
        }

        $stock = $variant ? $variant->stock : $product->stock;

        if ($stock < 1) {
            return back()->with('error', 'This product is out of stock.');
        }

        $qty = max(1, (int) $request->input('qty', 1));

        $cart = session('cart', []);
        $key = Cart::key($product->id, $variantId);
        $currentQty = $cart[$key] ?? 0;
        $cart[$key] = min($currentQty + $qty, $stock);
        session(['cart' => $cart]);

        $label = $product->name.($variant ? ' — '.$variant->label : '');

        return redirect()->route('cart.index')->with('success', $label.' added to cart.');
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $cart = session('cart', []);

        foreach ($request->input('qty', []) as $key => $qty) {
            $qty = (int) $qty;

            if ($qty <= 0 || ! isset($cart[$key])) {
                unset($cart[$key]);

                continue;
            }

            $parsed = Cart::parseKey($key);
            $product = Product::with('variants')->find($parsed['product_id']);
            $variant = $product && $parsed['variant_id'] ? $product->variants->firstWhere('id', $parsed['variant_id']) : null;
            $stock = $variant ? $variant->stock : ($product->stock ?? $qty);

            $cart[$key] = $product ? min($qty, max($stock, 1)) : $qty;
        }

        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            session()->flash('success', 'Cart updated.');

            return $this->cartFragmentResponse();
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $variantId = $request->integer('variant_id') ?: null;

        $cart = session('cart', []);
        unset($cart[Cart::key($product->id, $variantId)]);
        session(['cart' => $cart]);

        if ($request->wantsJson()) {
            session()->flash('success', 'Item removed from cart.');

            return $this->cartFragmentResponse();
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    /**
     * Re-render the cart head + body/empty fragment for the AJAX cart flow, so the
     * client can swap it in without a full page reload.
     */
    private function cartFragmentResponse(): JsonResponse
    {
        $cartItems = Cart::items();
        $totals = Cart::totals($cartItems);

        $html = view('partials.cart-head', compact('cartItems', 'totals'))->render()
            .($cartItems->isEmpty()
                ? view('partials.cart-empty')->render()
                : view('partials.cart-body', compact('cartItems', 'totals'))->render());

        return response()->json([
            'success' => true,
            'html' => $html,
            'cartCount' => array_sum(session('cart', [])),
        ]);
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
