<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\PageMeta;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\SuspendedCustomer;
use App\Services\BiteshipService;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

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
        $insuranceFee = (float) (Setting::where('key', 'insurance_fee')->value('value') ?? 15000);

        return view('pages.checkout', compact('pageMeta', 'cartItems', 'totals', 'insuranceFee'));
    }

    /**
     * Swap a cart line to another variant of the same product — the shopper picks the
     * product's option here on the checkout page rather than on the product page.
     */
    public function changeVariant(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart_key' => ['required', 'string'],
            'variant_id' => ['required', 'integer'],
        ]);

        $cart = session('cart', []);
        $oldKey = $data['cart_key'];

        if (! isset($cart[$oldKey])) {
            return response()->json(['success' => false, 'message' => 'That item is no longer in your cart.'], 422);
        }

        $product = Product::find(Cart::parseKey($oldKey)['product_id']);
        $variant = $product?->variants()->find($data['variant_id']);

        if (! $variant) {
            return response()->json(['success' => false, 'message' => 'That option isn\'t available.'], 422);
        }

        if ($variant->stock < 1) {
            return response()->json(['success' => false, 'message' => "\"{$variant->label}\" is out of stock."], 422);
        }

        $newKey = Cart::key($product->id, $variant->id);
        $message = null;

        if ($newKey !== $oldKey) {
            // Rebuild in the same order so the line doesn't jump to the bottom of the summary;
            // if the chosen variant is already in the cart the two lines merge.
            $wanted = $cart[$oldKey] + ($cart[$newKey] ?? 0);
            $qty = min($wanted, $variant->stock);
            $rebuilt = [];

            foreach ($cart as $key => $lineQty) {
                if ($key === $oldKey) {
                    $rebuilt[$newKey] = $qty;
                } elseif ($key !== $newKey) {
                    $rebuilt[$key] = $lineQty;
                }
            }

            session(['cart' => $rebuilt]);

            if ($qty < $wanted) {
                $message = "Only {$variant->stock} of \"{$variant->label}\" in stock — quantity adjusted.";
            }
        }

        $cartItems = Cart::items();
        $totals = Cart::totals($cartItems);

        return response()->json([
            'success' => true,
            'message' => $message,
            'html' => view('partials.checkout-summary', compact('cartItems', 'totals'))->render(),
        ]);
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

    /**
     * Resolve a postal code for a city/district string against Biteship's own delivery-area
     * database — used as a fallback when Nominatim's reverse-geocode didn't return a postcode
     * for the pinned point.
     */
    public function postalLookup(Request $request, BiteshipService $biteship): JsonResponse
    {
        $data = $request->validate([
            'query' => ['required', 'string', 'max:255'],
        ]);

        try {
            $areas = $biteship->searchAreas($data['query']);
        } catch (RuntimeException) {
            $areas = [];
        }

        $area = $areas[0] ?? null;

        if (! $area || empty($area['postal_code'])) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'postal_code' => (string) $area['postal_code'],
            'city' => $area['administrative_division_level_2_name'] ?? null,
        ]);
    }

    public function store(Request $request, BiteshipService $biteship): RedirectResponse
    {
        $cartItems = Cart::items();

        if ($cartItems->isEmpty()) {
            // A double-submitted form lands here after the first request already placed the
            // order and cleared the cart — send the shopper to that order instead of an error.
            $lastOrder = Order::find(session('last_order_id'));

            if ($lastOrder && $lastOrder->created_at->gt(now()->subMinutes(5))) {
                return redirect()->route('checkout.confirmation', $lastOrder);
            }

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
            'requested_delivery_date' => ['nullable', 'date', 'after:today', 'before_or_equal:'.now()->addDays(90)->toDateString()],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'courier_code' => ['nullable', 'string', 'max:100'],
            'courier_service_code' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,qris'],
            'has_insurance' => ['nullable', 'boolean'],
        ]);

        // A signed-in shopper is blocked by their own account status, in case they were
        // suspended/banned after their session started (status isn't re-checked until now).
        if ($request->user() && ! $request->user()->isActive()) {
            throw ValidationException::withMessages([
                'suspended' => 'We\'re sorry, this account is currently unable to place orders. Please contact support if you believe this is a mistake.',
            ]);
        }

        // Guests aren't logged in, so a suspension is matched against the email/phone they
        // just typed rather than an account — this is the earliest point either is known.
        if (SuspendedCustomer::activeMatch($data['email'], $data['phone'])) {
            throw ValidationException::withMessages([
                'suspended' => 'We\'re sorry, this account is currently unable to place orders. Please contact support if you believe this is a mistake.',
            ]);
        }

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
            fn (array $option) => $option['courier_code'] === ($data['courier_code'] ?? null)
                && $option['courier_service_code'] === ($data['courier_service_code'] ?? null)
        ) ?? collect($pricing)->sortBy('price')->first();

        $totals = Cart::totals($cartItems);
        $shippingFee = (float) $selectedCourier['price'];
        // Trust only the checkbox intent from the browser — the fee itself is re-read from
        // Settings server-side, the same guard used for the shipping fee above.
        $hasInsurance = $request->boolean('has_insurance');
        $insuranceFee = $hasInsurance ? (float) (Setting::where('key', 'insurance_fee')->value('value') ?? 15000) : 0.0;
        $grandTotal = max($totals['subtotal'] + $shippingFee + $totals['tax'] - $totals['promo_discount'] + $insuranceFee, 0);

        $order = DB::transaction(function () use ($data, $cartItems, $totals, $selectedCourier, $shippingFee, $hasInsurance, $insuranceFee, $grandTotal) {
            // Lock each product/variant row before checking stock so two shoppers checking out
            // the same low-stock item at once can't both pass the availability check.
            foreach ($cartItems as $item) {
                if ($item['variant']) {
                    $variant = ProductVariant::whereKey($item['variant']->id)->lockForUpdate()->first();

                    if ($variant->stock < $item['qty']) {
                        throw ValidationException::withMessages([
                            'stock' => "Sorry, only {$variant->stock} of \"{$item['product']->name} — {$variant->label}\" left in stock.",
                        ]);
                    }

                    continue;
                }

                $product = Product::whereKey($item['product']->id)->lockForUpdate()->first();

                if ($product->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'stock' => "Sorry, only {$product->stock} of \"{$product->name}\" left in stock.",
                    ]);
                }
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'notes' => $data['notes'] ?? null,
                'requested_delivery_date' => $data['requested_delivery_date'] ?? null,
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
                'has_insurance' => $hasInsurance,
                'insurance_fee' => $insuranceFee,
                'total' => $grandTotal,
                'status' => $data['payment_method'] === 'bank_transfer' ? 'awaiting_proof' : 'pending',
                'payment_method' => $data['payment_method'],
                'payment_proof_path' => null,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_variant_id' => $item['variant']?->id,
                    'product_name' => $item['product']->name,
                    'variant_label' => $item['variant']?->label,
                    'has_warranty' => $item['has_warranty'],
                    'unit_price' => $item['unit_price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                if ($item['variant']) {
                    ProductVariant::whereKey($item['variant']->id)->decrement('stock', $item['qty']);
                    $item['product']->syncFromVariants();
                } else {
                    Product::whereKey($item['product']->id)->decrement('stock', $item['qty']);
                }
            }

            return $order;
        });

        Cart::clear();
        session(['last_order_id' => $order->id]);

        try {
            Mail::to($order->email)->send(new OrderConfirmationMail($order));
        } catch (Throwable $e) {
            // A failed confirmation email shouldn't undo an already-placed order.
            Log::error('Failed to send order confirmation email', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        return redirect()->route('checkout.confirmation', $order);
    }

    public function confirmation(Order $order): View
    {
        // Only the browser that just placed this order may upload proof straight from here;
        // anyone else is sent through track-order, which verifies the order's email first.
        $canUploadProof = (int) session('last_order_id') === $order->id;

        return view('pages.checkout-confirmation', compact('order', 'canUploadProof'));
    }

    public function uploadProof(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) session('last_order_id') === $order->id, 403);
        abort_if($order->payment_method !== 'bank_transfer' || $order->status !== 'awaiting_proof', 404);

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:4096'],
        ]);

        $order->update([
            'payment_proof_path' => Storage::url($request->file('payment_proof')->store('payment-proofs', 'public')),
            'status' => 'pending',
        ]);

        return redirect()->route('checkout.confirmation', $order)
            ->with('success', 'Proof uploaded — we\'ll verify it and confirm your order shortly.');
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
                // Floor of 100g per unit: the catalog's weight field can be 0 until an admin
                // fills in the real figure, and Biteship rejects zero-weight items.
                'weight' => max((int) $item['weight'], 100),
            ])
            ->values()
            ->all();
    }
}
