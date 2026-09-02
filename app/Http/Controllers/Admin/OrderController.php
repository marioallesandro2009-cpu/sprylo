<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->get();

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

        DB::transaction(function () use ($order, $data) {
            $wasCancelled = $order->status === 'cancelled';
            $becomesCancelled = $data['status'] === 'cancelled';

            if ($becomesCancelled && ! $wasCancelled) {
                $this->adjustStock($order, 1);
            } elseif (! $becomesCancelled && $wasCancelled) {
                $this->adjustStock($order, -1);
            }

            $order->update(['status' => $data['status']]);
        });

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }

    /**
     * Restore stock (direction 1) when an order is cancelled, or re-deduct it (direction -1)
     * if a cancelled order is moved back to an active status.
     */
    private function adjustStock(Order $order, int $direction): void
    {
        foreach ($order->items as $item) {
            Product::whereKey($item->product_id)->increment('stock', $direction * $item->qty);
        }
    }
}
