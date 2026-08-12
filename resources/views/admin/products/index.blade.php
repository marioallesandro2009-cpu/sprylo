@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')

  <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
    <form method="GET" class="d-flex gap-2">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search products…" style="width:220px" />
      <select name="category" class="form-select form-select-sm" style="width:180px">
        <option value="">All categories</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Product</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            <tr>
              <td>
                @if($product->images->isNotEmpty())
                  <img src="{{ $product->images->first()->image }}" alt="{{ $product->name }}" style="width:44px;height:44px;object-fit:cover;border-radius:8px" />
                @else
                  <div class="bg-light" style="width:44px;height:44px;border-radius:8px"></div>
                @endif
              </td>
              <td class="fw-semibold">{{ $product->name }}</td>
              <td class="text-muted small">{{ $product->category->name }}</td>
              <td>
                ${{ number_format($product->sale_price ?? $product->price, 2) }}
                @if($product->sale_price)
                  <span class="text-muted text-decoration-line-through small">${{ number_format($product->price, 2) }}</span>
                @endif
              </td>
              <td>{{ $product->stock }}</td>
              <td>
                @if($product->is_active)
                  <span class="badge bg-success-subtle text-success">Active</span>
                @else
                  <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
