@extends('layouts.admin')

@section('title', 'Newsletter')
@section('page-title', 'Newsletter Subscribers')

@section('content')

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <form method="GET" class="d-flex gap-2">
      <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search email…" style="min-width:240px" />
      <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <a href="{{ route('admin.newsletter-subscribers.export') }}" class="btn btn-admin-primary"><i class="fa-solid fa-download me-1"></i> Export CSV</a>
  </div>

  <div class="card-admin">
    <div class="text-muted small mb-2">{{ $subscribers->total() }} subscriber(s)</div>
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Email</th><th>Subscribed</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($subscribers as $subscriber)
            <tr>
              <td class="fw-semibold">{{ $subscriber->email }}</td>
              <td class="text-muted small">{{ $subscriber->created_at->format('d M Y, H:i') }}</td>
              <td class="text-end">
                <form action="{{ route('admin.newsletter-subscribers.destroy', $subscriber) }}" method="POST" class="d-inline" data-confirm="Remove this subscriber?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted py-4">No subscribers yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $subscribers->links('pagination::bootstrap-5') }}
  </div>

@endsection