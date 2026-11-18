<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\DiscountSync;
use App\Support\StockLedger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $orders = Order::withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
        ]);

        $shortage = null;

        DB::transaction(function () use ($order, $data, &$shortage) {
            $wasCancelled = $order->status === 'cancelled';
            $becomesCancelled = $data['status'] === 'cancelled';

            if ($becomesCancelled && ! $wasCancelled) {
                $this->adjustStock($order, 1);
            } elseif (! $becomesCancelled && $wasCancelled) {
                // Re-deducting stock must not push it below zero.
                $shortage = $this->stockShortage($order);

                if ($shortage) {
                    return;
                }

                $this->adjustStock($order, -1);
            }

            $order->update(['status' => $data['status']]);
        });

        $this->resyncDiscounts();

        if ($shortage) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', "Can't reactivate this order: {$shortage}");
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order) {
            // A cancelled order has already returned its stock; any other status is still holding it.
            if ($order->status !== 'cancelled') {
                $this->adjustStock($order->load('items'), 1, true);
            }

            $order->delete();
        });

        $this->resyncDiscounts();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }

    /**
     * Describes the first item whose current stock can't cover this order, or null when all can.
     */
    private function stockShortage(Order $order): ?string
    {
        foreach ($order->items as $item) {
            $stock = $item->product_variant_id
                ? ProductVariant::whereKey($item->product_variant_id)->value('stock')
                : Product::whereKey($item->product_id)->value('stock');

            if ($stock === null) {
                return "{$item->product_name} no longer exists.";
            }

            if ($stock < $item->qty) {
                return "only {$stock} of {$item->product_name} left in stock, the order needs {$item->qty}.";
            }
        }

        return null;
    }

    /**
     * A cancelled/deleted flash-sale order frees quota (and a reactivated one uses it), which can
     * switch the promo price back on or off, so refresh the materialised promo prices.
     */
    private function resyncDiscounts(): void
    {
        DiscountSync::run();
    }

    /**
     * Restore stock (direction 1) when an order is cancelled, or re-deduct it (direction -1)
     * if a cancelled order is moved back to an active status.
     */
    private function adjustStock(Order $order, int $direction, bool $deleted = false): void
    {
        foreach ($order->items as $item) {
            // Units sold under a discount (flash-sale quota) go back / come out together with the stock.
            if ($item->discount_id) {
                $direction > 0
                    ? Discount::whereKey($item->discount_id)->update(['sold' => DB::raw('GREATEST(CAST(sold AS SIGNED) - '.(int) $item->qty.', 0)')])
                    : Discount::whereKey($item->discount_id)->increment('sold', $item->qty);
            }

            // update 1: pembatalan, pengaktifan ulang, dan hapus pesanan dicatat di riwayat stok
            $type = $direction > 0 ? 'return' : 'reactivate';
            $reason = $direction > 0 && $deleted ? "Pesanan {$order->order_number} dihapus" : null;

            if ($item->product_variant_id) {
                if ($variant = ProductVariant::find($item->product_variant_id)) {
                    StockLedger::move($variant, $direction * $item->qty, $type, $order, $reason);
                    $item->product?->syncFromVariants();
                }

                continue;
            }

            if ($product = Product::find($item->product_id)) {
                StockLedger::move($product, $direction * $item->qty, $type, $order, $reason);
            }
        }
    }
}
