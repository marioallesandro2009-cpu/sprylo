<?php

namespace App\Http\Controllers;

use App\Models\PageMeta;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $pageMeta = PageMeta::forPage('cart');

        $cart = session('cart', []);
        $products = Product::with('images')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        $cartItems = collect($cart)
            ->filter(fn ($qty, $productId) => $products->has($productId))
            ->map(function ($qty, $productId) use ($products) {
                $product = $products->get($productId);
                $unitPrice = $product->sale_price ?? $product->price;

                return [
                    'product' => $product,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $qty,
                ];
            })
            ->values();

        $subtotal = $cartItems->sum('subtotal');
        $shipping = ($subtotal > 0 && $subtotal < 50) ? 9.99 : 0.0;
        $tax = round($subtotal * 0.08, 2);
        $promoDiscount = 0.0;

        $totals = [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'promo_code' => null,
            'promo_discount' => $promoDiscount,
            'total' => $subtotal + $shipping + $tax - $promoDiscount,
        ];

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
}
