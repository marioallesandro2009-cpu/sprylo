@extends('layouts.admin')

@section('title', 'Brands')
@section('page-title', 'Brands')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.brands.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Brand</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Logo</th><th>Name</th><th>Website</th><th>Products</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($brands as $brand)
            <tr>
              <td>
                @if($brand->logo)
                  <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" style="width:40px;height:40px;object-fit:cover;border-radius:8px" />
                @else
                  <div class="bg-light" style="width:40px;height:40px;border-radius:8px"></div>
                @endif
              </td>
              <td class="fw-semibold">{{ $brand->name }}</td>
              <td class="text-muted small">{{ $brand->website_url ?: '—' }}</td>
              <td>{{ $brand->products_count }}</td>
              <td class="text-end">
                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" data-confirm="Delete this brand?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No brands yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
