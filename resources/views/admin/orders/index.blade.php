@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')

  <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
    <form method="GET" class="d-flex gap-2">
      <select name="status" class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()">
        <option value="">All statuses</option>
        @foreach(\App\Models\Order::STATUSES as $status)
          <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
        @endforeach
      </select>
      <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" style="width:260px"
             placeholder="Search order #, name, email or phone" aria-label="Search orders" />
      <button type="submit" class="btn btn-sm btn-admin-primary">Search</button>
      @if(request()->hasAny(['q', 'status']) && (request('q') !== null || request('status')))
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      @endif
    </form>
    <div class="text-muted small">{{ $orders->total() }} {{ \Illuminate\Support\Str::plural('order', $orders->total()) }}</div>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Placed</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <td class="fw-semibold">{{ $order->order_number }}</td>
              <td>{{ $order->name }}<div class="text-muted small">{{ $order->email }}</div></td>
              <td>{{ $order->items_count }}</td>
              <td>{{ rupiah($order->total) }}</td>
              <td><span class="badge {{ $order->status_badge_class }} text-capitalize">{{ str_replace('_', ' ', $order->status) }}</span></td>
              <td class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</td>
              <td class="text-end">
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-eye"></i></a>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" data-confirm="Delete this order?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">{{ request()->hasAny(['q', 'status']) ? 'No orders match your search.' : 'No orders yet.' }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($orders->hasPages())
    <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
  @endif

@endsection
