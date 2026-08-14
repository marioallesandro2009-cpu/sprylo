@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')

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
              <td><span class="badge bg-primary-subtle text-primary text-capitalize">{{ $order->status }}</span></td>
              <td class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</td>
              <td class="text-end">
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-eye"></i></a>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this order?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No orders yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
