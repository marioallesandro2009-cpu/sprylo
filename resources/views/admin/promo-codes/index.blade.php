@extends('layouts.admin')

@section('title', 'Promo Codes')
@section('page-title', 'Promo Codes')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.promo-codes.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Promo Code</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Code</th><th>Discount</th><th>Min. subtotal</th><th>Expires</th><th>Status</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($promoCodes as $promoCode)
            <tr>
              <td class="fw-semibold">{{ $promoCode->code }}</td>
              <td>{{ $promoCode->type === 'percent' ? $promoCode->value.'%' : rupiah($promoCode->value) }}</td>
              <td class="text-muted small">{{ $promoCode->min_subtotal ? rupiah($promoCode->min_subtotal) : '—' }}</td>
              <td class="text-muted small">{{ $promoCode->expires_at?->format('d M Y') ?? '—' }}</td>
              <td>
                @if($promoCode->is_active)
                  <span class="badge bg-success-subtle text-success">Active</span>
                @else
                  <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.promo-codes.edit', $promoCode) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.promo-codes.destroy', $promoCode) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this promo code?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No promo codes yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
