<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PageMeta;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(Request $request): View
    {
        $pageMeta = PageMeta::forPage('track-order');

        return view('pages.track-order', [
            'pageMeta' => $pageMeta,
            'order' => null,
            'verifiedEmail' => null,
            'prefillOrderNumber' => $request->query('order_number'),
        ]);
    }

    public function lookup(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $order = Order::where('order_number', strtoupper(trim($data['order_number'])))
            ->where('email', $data['email'])
            ->first();

        if (! $order) {
            return back()
                ->withErrors(['order_number' => "No order found matching that order number and email."])
                ->withInput();
        }

        $order->load('items');

        return view('pages.track-order', [
            'pageMeta' => PageMeta::forPage('track-order'),
            'order' => $order,
            'verifiedEmail' => $data['email'],
            'prefillOrderNumber' => $order->order_number,
        ]);
    }

    public function uploadProof(Request $request, Order $order): View
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'payment_proof' => ['required', 'image', 'max:4096'],
        ]);

        abort_if($data['email'] !== $order->email, 403);
        abort_if($order->payment_method !== 'bank_transfer' || $order->status !== 'awaiting_proof', 404);

        $order->update([
            'payment_proof_path' => Storage::url($request->file('payment_proof')->store('payment-proofs', 'public')),
            'status' => 'pending',
        ]);

        $order->load('items');

        return view('pages.track-order', [
            'pageMeta' => PageMeta::forPage('track-order'),
            'order' => $order,
            'verifiedEmail' => $data['email'],
            'prefillOrderNumber' => $order->order_number,
        ])->with('success', 'Proof uploaded — we\'ll verify it and confirm your order shortly.');
    }

    public function reorder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        abort_if($data['email'] !== $order->email, 403);

        $order->load('items.product', 'items.productVariant');

        $cart = session('cart', []);
        $added = 0;
        $unavailable = 0;
        $reduced = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            $variant = $item->productVariant;

            if (! $product || ! $product->is_active || ($product->has_variants && ! $variant)) {
                $unavailable++;

                continue;
            }

            $stock = $variant ? $variant->stock : $product->stock;

            if ($stock < 1) {
                $unavailable++;

                continue;
            }

            $key = Cart::key($product->id, $variant?->id);
            $currentQty = $cart[$key] ?? 0;
            $desiredQty = $currentQty + $item->qty;
            $newQty = min($desiredQty, $stock);

            if ($newQty < $desiredQty) {
                $reduced++;
            }

            $cart[$key] = $newQty;
            $added++;
        }

        session(['cart' => $cart]);

        if ($added === 0) {
            return redirect()->route('track-order.index', ['order_number' => $order->order_number])
                ->with('error', 'None of the items from this order are available to reorder right now.');
        }

        $message = $added.' item'.($added === 1 ? '' : 's').' from this order '.($added === 1 ? 'was' : 'were').' added to your cart.';

        if ($unavailable > 0) {
            $message .= ' '.$unavailable.' item'.($unavailable === 1 ? '' : 's').' could not be added (out of stock or no longer available).';
        }

        if ($reduced > 0) {
            $message .= ' Some quantities were reduced to match available stock.';
        }

        return redirect()->route('cart.index')->with('success', $message);
    }
}
