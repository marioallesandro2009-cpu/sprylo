@extends('layouts.admin')

@section('title', 'Page SEO')
@section('page-title', 'Page SEO')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.page-meta.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Page</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Page</th><th>Title</th><th>Meta description</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($pages as $page)
            <tr>
              <td class="fw-semibold">{{ $page->page_slug }}</td>
              <td>{{ $page->title }}</td>
              <td class="text-muted small">{{ \Illuminate\Support\Str::limit($page->meta_description, 90) }}</td>
              <td class="text-end">
                <a href="{{ route('admin.page-meta.edit', $page) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.page-meta.destroy', $page) }}" method="POST" class="d-inline" data-confirm="Delete this page SEO entry?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No page SEO entries yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
