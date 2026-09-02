<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PageMeta;
use App\Models\Setting;
use App\Services\BiteshipService;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

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

    /**
     * Look up available couriers & shipping cost for a pinned destination, called from the
     * checkout page before the order is placed.
     */
    public function rates(Request $request, BiteshipService $biteship): JsonResponse
    {
        $cartItems = Cart::items();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        try {
            $pricing = $biteship->getRates(
                ['latitude' => (float) $data['latitude'], 'longitude' => (float) $data['longitude']],
                $this->cartToBiteshipItems($cartItems),
            );
        } catch (RuntimeException) {
            $pricing = [];
        }

        // Biteship only covers Indonesian addresses, so any destination it can't price
        // (including addresses outside Indonesia) still gets a deliverable option.
        if (empty($pricing)) {
            $pricing = [$this->flatRateCourier(Cart::totals($cartItems)['subtotal'])];
        }

        $couriers = collect($pricing)
            ->map(fn (array $option) => [
                'courier_code' => $option['courier_code'],
                'courier_service_code' => $option['courier_service_code'],
                'courier_name' => $option['courier_name'],
                'service_name' => $option['courier_service_name'],
                'price' => (float) $option['price'],
                'duration' => $option['duration'] ?? $option['shipment_duration_range'] ?? null,
            ])
            ->sortBy('price')
            ->values();

        return response()->json(['success' => true, 'couriers' => $couriers]);
    }

    public function store(Request $request, BiteshipService $biteship): RedirectResponse
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
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'courier_code' => ['nullable', 'string', 'max:100'],
            'courier_service_code' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,qris'],
            'payment_proof' => ['required_if:payment_method,bank_transfer', 'image', 'max:4096'],
        ]);

        // Re-fetch the rate from Biteship instead of trusting the price submitted by the
        // browser, so a tampered "shipping fee" field can't be used to undercut the order total.
        try {
            $pricing = $biteship->getRates(
                ['latitude' => (float) $data['latitude'], 'longitude' => (float) $data['longitude']],
                $this->cartToBiteshipItems($cartItems),
            );
        } catch (RuntimeException) {
            $pricing = [];
        }

        // Same fallback as rates(): Biteship can't price addresses outside Indonesia, so fall
        // back to the flat shipping fee rather than blocking the order.
        if (empty($pricing)) {
            $pricing = [$this->flatRateCourier(Cart::totals($cartItems)['subtotal'])];
        }

        // If the shopper's browser hadn't finished fetching/selecting a courier yet
        // (or JS didn't run), fall back to the cheapest available option instead of
        // blocking the order.
        $selectedCourier = collect($pricing)->first(
            fn (array $option) => $option['courier_code'] === $data['courier_code']
                && $option['courier_service_code'] === $data['courier_service_code']
        ) ?? collect($pricing)->sortBy('price')->first();

        $totals = Cart::totals($cartItems);
        $shippingFee = (float) $selectedCourier['price'];
        $grandTotal = max($totals['subtotal'] + $shippingFee + $totals['tax'] - $totals['promo_discount'], 0);

        $paymentProofPath = $data['payment_method'] === 'bank_transfer'
            ? Storage::url($request->file('payment_proof')->store('payment-proofs', 'public'))
            : null;

        $order = DB::transaction(function () use ($data, $cartItems, $totals, $selectedCourier, $shippingFee, $grandTotal, $paymentProofPath) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'notes' => $data['notes'] ?? null,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'courier_code' => $selectedCourier['courier_code'],
                'courier_service_code' => $selectedCourier['courier_service_code'],
                'courier_name' => $selectedCourier['courier_name'],
                'courier_service_name' => $selectedCourier['courier_service_name'],
                'shipping_duration' => $selectedCourier['duration'] ?? $selectedCourier['shipment_duration_range'] ?? null,
                'subtotal' => $totals['subtotal'],
                'shipping' => $shippingFee,
                'tax' => $totals['tax'],
                'promo_code' => $totals['promo_code'],
                'promo_discount' => $totals['promo_discount'],
                'total' => $grandTotal,
                'payment_method' => $data['payment_method'],
                'payment_proof_path' => $paymentProofPath,
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

    /**
     * Standard-shipping fallback used whenever Biteship has no courier for the
     * pinned destination (e.g. it's outside Indonesia), keyed the same way as a
     * Biteship pricing option so it can be selected/verified through the same code path.
     *
     * @return array{courier_code: string, courier_service_code: string, courier_name: string, courier_service_name: string, price: float, duration: null}
     */
    private function flatRateCourier(float $subtotal): array
    {
        $freeThreshold = (float) (Setting::where('key', 'shipping_free_threshold')->value('value') ?? 750000);
        $shippingFee = (float) (Setting::where('key', 'shipping_fee')->value('value') ?? 25000);
        $price = ($subtotal > 0 && $subtotal < $freeThreshold) ? $shippingFee : 0.0;

        return [
            'courier_code' => 'flat',
            'courier_service_code' => 'standard',
            'courier_name' => 'Standard Shipping',
            'courier_service_name' => 'Flat rate',
            'price' => $price,
            'duration' => null,
        ];
    }

    /**
     * @return array<int, array{name: string, value: float, quantity: int, weight: int}>
     */
    private function cartToBiteshipItems(Collection $cartItems): array
    {
        return $cartItems
            ->map(fn (array $item) => [
                'name' => $item['product']->name,
                'value' => (float) $item['unit_price'],
                'quantity' => $item['qty'],
                // Floor of 100g per unit: the product catalog's weight field can be 0 until
                // an admin fills in the real figure, and Biteship rejects zero-weight items.
                'weight' => max((int) $item['product']->weight, 100),
            ])
            ->values()
            ->all();
    }
}
