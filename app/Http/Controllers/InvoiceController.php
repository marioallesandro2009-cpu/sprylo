<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves an order's invoice as a PDF. Two routes point here: the signed customer link
 * (invoices.show) and the admin button (admin.orders.invoice).
 */
class InvoiceController extends Controller
{
    public function show(Request $request, Order $order, InvoiceService $invoices): Response
    {
        abort_unless($invoices->canInvoice($order), 404, 'A cancelled order has no invoice.');

        $pdf = $invoices->pdf($order);
        $filename = $invoices->filename($order->refresh());

        // ?download=1 saves the file; otherwise it opens in the browser's PDF viewer.
        return $request->boolean('download') ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
