@extends('layouts.admin')

@section('title', 'Promo Banners')
@section('page-title', 'Promo Banners')

@section('content')

  <p class="text-muted small mb-3">These are the discount banners on the homepage (below Trending Products). Add or remove as many as you like — pick any product that has a sale price.</p>

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.promo-banners.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Banner</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr>
            <th>#</th><th>Product</th><th>Badge label</th><th>Discount</th><th>Theme</th><th>Status</th><th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($banners as $banner)
            <tr>
              <td class="text-muted small">{{ $banner->sort_order }}</td>
              <td class="fw-semibold">{{ $banner->product->name ?? '—' }}</td>
              <td>{{ $banner->label }}</td>
              <td>{{ $banner->discount_percent !== null ? $banner->discount_percent.'% off' : '—' }}</td>
              <td class="text-capitalize">{{ \App\Models\PromoBanner::THEMES[$banner->theme] ?? $banner->theme }}</td>
              <td>
                @if($banner->is_active)
                  <span class="badge text-bg-success">Active</span>
                @else
                  <span class="badge text-bg-secondary">Hidden</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.promo-banners.edit', $banner) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.promo-banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this banner?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No promo banners yet — add one to show a discount banner on the homepage.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
