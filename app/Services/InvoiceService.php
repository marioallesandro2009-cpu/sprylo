<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Invoices are generated from an order on demand: the PDF itself is never stored, only the invoice
 * number and issue date (on the order), so an invoice always reflects the order as it is now.
 */
class InvoiceService
{
    /** Order statuses where the customer has paid (or the shop has verified payment). */
    private const PAID_STATUSES = ['paid', 'processing', 'shipped', 'completed'];

    public function canInvoice(Order $order): bool
    {
        return $order->status !== 'cancelled';
    }

    /**
     * The invoice number for this order, assigning the next one in the month's sequence the first time.
     * Numbers are never reused or changed, so a customer's copy and the shop's copy always match.
     */
    public function ensureNumber(Order $order): string
    {
        if ($order->invoice_number) {
            return $order->invoice_number;
        }

        // The unique index is the real guard; a second request that races for the same number just retries.
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                return DB::transaction(function () use ($order) {
                    $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

                    if ($order->invoice_number) {
                        return $order->invoice_number;
                    }

                    $prefix = 'INV-'.now(Discount::DISPLAY_TZ)->format('ym').'-';
                    $last = Order::where('invoice_number', 'like', $prefix.'%')->orderByDesc('invoice_number')->value('invoice_number');
                    $next = $last ? ((int) substr($last, -4)) + 1 : 1;

                    $order->forceFill([
                        'invoice_number' => $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                        'invoiced_at' => now(),
                    ])->save();

                    return $order->invoice_number;
                });
            } catch (QueryException $e) {
                if ($attempt === 3) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Could not allocate an invoice number.');
    }

    /** Builds the PDF (assigning an invoice number first if the order doesn't have one yet). */
    public function pdf(Order $order): PdfDocument
    {
        $this->ensureNumber($order);
        $order->refresh()->load('items');

        return Pdf::loadView('invoices.pdf', $this->data($order))
            ->setPaper('a4', 'portrait')
            ->setOption(['defaultFont' => 'DejaVu Sans', 'isRemoteEnabled' => false, 'dpi' => 96]);
    }

    public function filename(Order $order): string
    {
        return 'Invoice-'.($order->invoice_number ?? $order->order_number).'.pdf';
    }

    /** Everything the invoice template prints. */
    public function data(Order $order): array
    {
        $settings = Setting::pluck('value', 'key');
        $tz = Discount::DISPLAY_TZ;
        $paid = in_array($order->status, self::PAID_STATUSES, true);

        return [
            'order' => $order,
            'seller' => [
                'name' => $settings['invoice_company_name'] ?? null ?: ($settings['site_name'] ?? config('app.name')),
                'address' => $settings['contact_address'] ?? null,
                'email' => $settings['contact_email'] ?? null,
                'phone' => $settings['contact_phone'] ?? null,
            ],
            'bank' => [
                'name' => $settings['payment_bank_name'] ?? null,
                'number' => $settings['payment_bank_account_number'] ?? null,
                'holder' => $settings['payment_bank_account_holder'] ?? null,
            ],
            'footerNote' => $settings['invoice_footer_note'] ?? null,
            'issuedAt' => ($order->invoiced_at ?? now())->copy()->timezone($tz),
            'placedAt' => $order->created_at->copy()->timezone($tz),
            'paid' => $paid,
            'statusLabel' => $paid ? 'PAID' : match ($order->status) {
                'cancelled' => 'CANCELLED',
                'pending' => 'PAYMENT PENDING VERIFICATION',
                default => 'UNPAID',
            },
            'paymentLabel' => $order->payment_method === 'qris' ? 'QRIS' : 'Bank transfer',
        ];
    }
}
