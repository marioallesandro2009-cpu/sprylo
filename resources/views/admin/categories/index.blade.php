@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.categories.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Category</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <td>
                @if($category->image)
                  <img src="{{ $category->image }}" alt="{{ $category->name }}" style="width:44px;height:44px;object-fit:cover;border-radius:8px" />
                @else
                  <div class="bg-light" style="width:44px;height:44px;border-radius:8px"></div>
                @endif
              </td>
              <td class="fw-semibold">{{ $category->name }}</td>
              <td class="text-muted small">{{ $category->slug }}</td>
              <td>{{ $category->products_count }}</td>
              <td class="text-end">
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No categories yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
