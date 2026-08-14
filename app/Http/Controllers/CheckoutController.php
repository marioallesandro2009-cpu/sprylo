<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PageMeta;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cartItems = Cart::items();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $pageMeta = PageMeta::forPage('checkout');
        $totals = Cart::totals($cartItems);

        return view('pages.checkout', compact('pageMeta', 'cartItems', 'totals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cartItems = Cart::items();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $totals = Cart::totals($cartItems);

        $order = DB::transaction(function () use ($data, $cartItems, $totals) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $totals['subtotal'],
                'shipping' => $totals['shipping'],
                'tax' => $totals['tax'],
                'promo_code' => $totals['promo_code'],
                'promo_discount' => $totals['promo_discount'],
                'total' => $totals['total'],
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'unit_price' => $item['unit_price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        Cart::clear();

        return redirect()->route('checkout.confirmation', $order);
    }

    public function confirmation(Order $order): View
    {
        return view('pages.checkout-confirmation', compact('order'));
    }
}
