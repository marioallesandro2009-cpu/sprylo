@extends('layouts.admin')

@section('title', 'Navigation')
@section('page-title', 'Navigation')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.nav-links.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Menu Link</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>#</th><th>Label</th><th>URL</th><th>Shown in</th><th>Status</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($navLinks as $link)
            <tr>
              <td class="text-muted small">{{ $link->sort_order }}</td>
              <td class="fw-semibold">{{ $link->label }}</td>
              <td class="text-muted small">{{ $link->url }}</td>
              <td class="text-muted small">{{ \App\Models\NavLink::PLACEMENTS[$link->placement] ?? $link->placement }}</td>
              <td>
                @if($link->is_active)
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">Hidden</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.nav-links.edit', $link) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.nav-links.destroy', $link) }}" method="POST" class="d-inline" data-confirm="Delete this menu link?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No menu links yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
