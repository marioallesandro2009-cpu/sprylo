@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)
@section('page-title', 'Order Detail')

@section('content')

  <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to orders</a>
    @if($order->status !== 'cancelled')
      <span class="ms-auto d-inline-flex gap-2 align-items-center">
        @if($order->invoice_number)<span class="text-muted small">Invoice <strong>{{ $order->invoice_number }}</strong></span>@endif
        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" rel="noopener" class="btn btn-sm btn-admin-primary"><i class="fa-solid fa-file-invoice me-1"></i> {{ $order->invoice_number ? 'View invoice' : 'Generate invoice' }} (PDF)</a>
        <a href="{{ route('admin.orders.invoice', [$order, 'download' => 1]) }}" class="btn btn-sm btn-outline-secondary" title="Download PDF"><i class="fa-solid fa-download"></i></a>
      </span>
    @endif
  </div>

  <div class="card-admin mb-3" style="max-width:720px">
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <div class="text-muted small">Order number</div>
        <div class="fw-semibold">{{ $order->order_number }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Placed</div>
        <div class="fw-semibold">{{ $order->created_at->format('d M Y, H:i') }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Customer</div>
        <div class="fw-semibold">{{ $order->name }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Email</div>
        <div class="fw-semibold"><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Phone</div>
        <div class="fw-semibold">{{ $order->phone }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Status</div>
        <div class="fw-semibold">
          <span class="badge {{ $order->status_badge_class }} text-capitalize">{{ str_replace('_', ' ', $order->status) }}</span>
        </div>
      </div>
      <div class="col-12">
        <div class="text-muted small">Shipping address</div>
        <div class="fw-semibold">{{ $order->address }}, {{ $order->city }} {{ $order->postal_code }}</div>
        @if($order->latitude && $order->longitude)
          <div class="text-muted small">
            <a href="https://www.openstreetmap.org/?mlat={{ $order->latitude }}&mlon={{ $order->longitude }}#map=16/{{ $order->latitude }}/{{ $order->longitude }}" target="_blank" rel="noopener">
              View pinned location ({{ $order->latitude }}, {{ $order->longitude }})
            </a>
          </div>
        @endif
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Courier</div>
        <div class="fw-semibold">
          @if($order->courier_name)
            {{ $order->courier_name }} — {{ $order->courier_service_name }}
            @if($order->shipping_duration) <span class="text-muted">({{ $order->shipping_duration }})</span> @endif
          @else
            —
          @endif
        </div>
      </div>
      @if($order->requested_delivery_date)
        <div class="col-md-6">
          <div class="text-muted small">Requested delivery schedule</div>
          <div class="fw-semibold">
            {{ $order->delivery_schedule }}
            @if($order->requested_delivery_date->isToday() || $order->requested_delivery_date->isTomorrow())
              <span class="badge bg-warning-subtle text-warning-emphasis">Coming up soon</span>
            @endif
          </div>
        </div>
      @endif
      <div class="col-md-6">
        <div class="text-muted small">Payment method</div>
        <div class="fw-semibold text-capitalize">
          {{ $order->payment_method ? str_replace('_', ' ', $order->payment_method) : '—' }}
          @if($order->payment_proof_path)
            · <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener">view proof</a>
          @endif
        </div>
      </div>
      @if($order->notes)
        <div class="col-12">
          <div class="text-muted small">Notes</div>
          <div class="fw-semibold" style="white-space:pre-line">{{ $order->notes }}</div>
        </div>
      @endif
    </div>
  </div>

  <div class="card-admin" style="max-width:720px">
    <div class="table-responsive">
      <table class="table table-admin align-middle mb-3">
        <thead>
          <tr><th>Product</th><th>Qty</th><th>Unit price</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
            <tr>
              <td>
                {{ $item->product_name }}@if($item->variant_label) <span class="text-muted small">({{ $item->variant_label }})</span>@endif
                @if($item->product_variant_id)
                  @if($item->has_warranty)
                    <span class="badge bg-success-subtle text-success-emphasis">Warranty incl.</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">No warranty</span>
                  @endif
                @endif
              </td>
              <td>{{ $item->qty }}</td>
              <td>{{ rupiah($item->unit_price) }}</td>
              <td>{{ rupiah($item->subtotal) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="d-flex flex-column align-items-end gap-1">
      <div class="text-muted small">Subtotal: {{ rupiah($order->subtotal) }}</div>
      <div class="text-muted small">Shipping: {{ $order->shipping == 0.0 ? 'Free' : rupiah($order->shipping) }}</div>
      @if($order->has_insurance)
        <div class="text-muted small">Shipping insurance: {{ rupiah($order->insurance_fee) }}</div>
      @endif
      @if($order->promo_discount > 0)
        <div class="text-muted small">Promo ({{ $order->promo_code }}): −{{ rupiah($order->promo_discount) }}</div>
      @endif
      <div class="text-muted small">{{ $order->tax_label }}: {{ rupiah($order->tax) }}</div>
      <div class="fw-bold fs-5">Total: {{ rupiah($order->total) }}</div>
    </div>
  </div>

  <div class="card-admin mt-3" style="max-width:720px">
    <div class="text-muted small mb-2">Update status</div>
    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-flex gap-2">
      @csrf @method('PATCH')
      <select name="status" class="form-select form-select-sm" style="width:200px">
        @foreach(\App\Models\Order::STATUSES as $status)
          <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-admin-primary btn-sm">Save status</button>
    </form>
    @if($order->status !== 'cancelled')
      <div class="text-muted small mt-2">Cancelling restores the ordered items to stock; moving a cancelled order back to an active status deducts stock again.</div>
    @endif
  </div>

  <div class="mt-3">
    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" data-confirm="Delete this order?">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash me-1"></i> Delete order</button>
    </form>
  </div>

@endsection
