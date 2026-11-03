@include('partials.order-totals')

<div style="margin-top: var(--s6); padding-top: var(--s6); border-top: 1px solid var(--rule)">
  <div class="label" style="font-family: var(--ff-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; color: var(--fg-mute); margin-bottom: var(--s2)">Shipping to</div>
  <div style="font-weight:600">{{ $order->name }}</div>
  <div style="color: var(--fg-soft); font-size: var(--text-sm); white-space: pre-line">{{ $order->address }}
{{ $order->city }}, {{ $order->postal_code }}</div>
  <div style="color: var(--fg-soft); font-size: var(--text-sm); margin-top: var(--s2)">{{ $order->phone }} · {{ $order->email }}</div>
  @if($order->requested_delivery_date)
    <div style="color: var(--fg-soft); font-size: var(--text-sm); margin-top: var(--s2)">Requested delivery: {{ $order->delivery_schedule }}</div>
  @endif
  @if($order->status !== 'cancelled')
    {{-- Signed link: only someone who was shown this page can open the invoice, and it can't be guessed from the order number. --}}
    <div style="margin-top: var(--s4)">
      <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('invoices.show', now()->addDays(7), ['order' => $order]) }}" target="_blank" rel="noopener" class="btn btn--ghost">⬇ Invoice (PDF)</a>
    </div>
  @endif
</div>
