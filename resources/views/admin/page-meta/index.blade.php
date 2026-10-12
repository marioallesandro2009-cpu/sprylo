@extends('layouts.admin')

@section('title', 'SEO / Page Meta')
@section('page-title', 'SEO / Page Meta')

@section('content')

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Page</th><th>Title</th><th>Meta description</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($pages as $page)
            <tr>
              <td><code>{{ $page->page_slug }}</code></td>
              <td class="fw-semibold">{{ $page->title ?: '—' }}</td>
              <td class="text-muted small">{{ \Illuminate\Support\Str::limit($page->meta_description, 90) ?: '—' }}</td>
              <td class="text-end">
                <a href="{{ route('admin.page-meta.edit', $page) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No pages yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection