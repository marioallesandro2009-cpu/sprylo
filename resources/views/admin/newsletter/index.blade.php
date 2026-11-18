@extends('layouts.admin')

@section('title', 'Newsletter')
@section('page-title', 'Newsletter')

@section('content')

  <div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">{{ $subscribers->total() }} subscriber(s)</span>
    <a href="{{ route('admin.newsletter-subscribers.export') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-download me-1"></i> Export CSV</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Email</th><th>Subscribed</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($subscribers as $subscriber)
            <tr>
              <td class="fw-semibold">{{ $subscriber->email }}</td>
              <td class="text-muted small">{{ $subscriber->created_at->format('d M Y H:i') }}</td>
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
  </div>

  <div class="mt-3">{{ $subscribers->links() }}</div>

@endsection
