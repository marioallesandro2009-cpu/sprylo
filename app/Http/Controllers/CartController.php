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
        if (! $product->is_active) {
            return back()->with('error', 'This product is no longer available.');
        }

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

        // A flash sale only has so many units at its price; don't let the cart exceed what's left.
        $flashLeft = ($variant ?? $product)->promo?->remaining;
        $cap = $flashLeft !== null ? min($stock, $flashLeft) : $stock;

        $cart = session('cart', []);
        $key = Cart::key($product->id, $variantId);
        $currentQty = $cart[$key] ?? 0;
        $requested = $currentQty + $qty;
        $cart[$key] = min($requested, $cap);
        session(['cart' => $cart]);

        $label = $product->name.($variant ? ' — '.$variant->label : '');

        if ($requested > $cap) {
            $reason = $flashLeft !== null && $flashLeft < $stock ? 'left in the flash sale' : 'in stock';

            return redirect()->route('cart.index')
                ->with('warning', "Only {$cap} of {$label} {$reason} — your cart now has {$cap}.");
        }

        return redirect()->route('cart.index')->with('success', $label.' added to cart.');
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $cart = session('cart', []);
        $adjusted = [];

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
            $flashLeft = $product ? ($variant ?? $product)->promo?->remaining : null;
            $cap = $flashLeft !== null ? min($stock, $flashLeft) : $stock;

            $cart[$key] = $product ? min($qty, max($cap, 1)) : $qty;

            if ($product && $cart[$key] < $qty) {
                $reason = $flashLeft !== null && $flashLeft < $stock ? 'left in the flash sale' : 'in stock';
                $adjusted[] = "only {$cart[$key]} of {$product->name} {$reason}";
            }
        }

        session(['cart' => $cart]);

        $warning = $adjusted ? 'Quantity adjusted: '.implode('; ', $adjusted).'.' : null;

        if ($request->wantsJson()) {
            return $this->cartFragmentResponse($warning);
        }

        return redirect()->route('cart.index')->with($warning ? 'warning' : 'success', $warning ?? 'Cart updated.');
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
    private function cartFragmentResponse(?string $warning = null): JsonResponse
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
            'warning' => $warning,
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
