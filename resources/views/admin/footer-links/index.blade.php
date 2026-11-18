@extends('layouts.admin')

@section('title', 'Footer Links')
@section('page-title', 'Footer Links')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.footer-links.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Link</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Column</th><th>Label</th><th>URL</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($footerLinks as $link)
            <tr>
              <td class="text-muted small">{{ \App\Models\FooterLink::GROUPS[$link->group] ?? $link->group }}</td>
              <td class="fw-semibold">{{ $link->label }}</td>
              <td class="text-muted small">{{ $link->url }}</td>
              <td class="text-end">
                <a href="{{ route('admin.footer-links.edit', $link) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.footer-links.destroy', $link) }}" method="POST" class="d-inline" data-confirm="Delete this link?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No footer links yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
