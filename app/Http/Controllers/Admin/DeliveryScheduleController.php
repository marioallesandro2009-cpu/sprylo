<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Support\DeliverySlots;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Agenda of the delivery dates and time windows customers asked for, so dispatch can plan each day.
 */
class DeliveryScheduleController extends Controller
{
    /** Statuses where the parcel has not left yet, so a passed delivery date means it is running late. */
    private const NOT_SHIPPED = ['awaiting_proof', 'pending', 'paid', 'processing'];

    public function index(Request $request): View
    {
        $tz = Discount::DISPLAY_TZ;
        $today = Carbon::now($tz)->startOfDay();

        $days = in_array((int) $request->query('days'), [7, 14, 30], true) ? (int) $request->query('days') : 14;
        $from = $this->validDate($request->query('from')) ? Carbon::parse($request->query('from'), $tz)->startOfDay() : $today->copy();
        $to = $from->copy()->addDays($days - 1);

        $scheduled = Order::whereNotNull('requested_delivery_date')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('requested_delivery_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('requested_delivery_date')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn (Order $order) => $order->requested_delivery_date->toDateString());

        $overdue = Order::whereNotNull('requested_delivery_date')
            ->whereDate('requested_delivery_date', '<', $today->toDateString())
            ->whereIn('status', self::NOT_SHIPPED)
            ->orderBy('requested_delivery_date')
            ->get();

        $stats = [
            'range' => $scheduled->flatten()->count(),
            'today' => Order::whereDate('requested_delivery_date', $today->toDateString())->where('status', '!=', 'cancelled')->count(),
            'tomorrow' => Order::whereDate('requested_delivery_date', $today->copy()->addDay()->toDateString())->where('status', '!=', 'cancelled')->count(),
            'overdue' => $overdue->count(),
        ];

        return view('admin.delivery-schedule.index', [
            'scheduled' => $scheduled,
            'overdue' => $overdue,
            'stats' => $stats,
            'slots' => DeliverySlots::SLOTS,
            'capacity' => DeliverySlots::capacity(),
            'today' => $today,
            'from' => $from,
            'to' => $to,
            'days' => $days,
        ]);
    }

    private function validDate(mixed $value): bool
    {
        return is_string($value) && (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) !== false;
    }
}
