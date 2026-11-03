<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PageMeta;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lets a customer cross-check an invoice they hold against the shop's records. Invoice numbers run in
 * sequence and can be guessed, so the order's email is required too.
 */
class InvoiceCheckController extends Controller
{
    public function index(): View
    {
        return view('pages.invoice-check', [
            'pageMeta' => PageMeta::forPage('invoice-check'),
            'invoice' => null,
        ]);
    }

    public function lookup(Request $request, InvoiceService $invoices): View|RedirectResponse
    {
        $data = $request->validate([
            'invoice_number' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $order = Order::where('invoice_number', strtoupper(trim($data['invoice_number'])))
            ->where('email', $data['email'])
            ->first();

        if (! $order) {
            return back()
                ->withErrors(['invoice_number' => 'No invoice found matching that invoice number and email.'])
                ->withInput();
        }

        $order->load('items');

        return view('pages.invoice-check', [
            'pageMeta' => PageMeta::forPage('invoice-check'),
            'invoice' => $invoices->data($order),
        ]);
    }
}
