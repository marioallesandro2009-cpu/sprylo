@extends('layouts.admin')

@section('title', 'Contact Messages')
@section('page-title', 'Contact Messages')

@section('content')

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Status</th><th>Name</th><th>Email</th><th>Topic</th><th>Received</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($messages as $message)
            <tr class="{{ $message->is_read ? '' : 'fw-semibold' }}">
              <td>
                @if($message->is_read)
                  <span class="badge bg-secondary-subtle text-secondary">Read</span>
                @else
                  <span class="badge bg-primary">New</span>
                @endif
              </td>
              <td>{{ $message->first_name }} {{ $message->last_name }}</td>
              <td class="text-muted small">{{ $message->email }}</td>
              <td>{{ $message->topic }}</td>
              <td class="text-muted small">{{ $message->created_at->format('d M Y, H:i') }}</td>
              <td class="text-end">
                <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-eye"></i></a>
                <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No messages yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
