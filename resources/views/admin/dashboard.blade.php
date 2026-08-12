@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

  <div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
      <div class="card-admin">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-muted mb-1" style="font-size:.8rem">Products</p>
            <h4 class="fw-bold mb-0">{{ $stats['products'] }}</h4>
          </div>
          <i class="fa-solid fa-box text-primary fs-3"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="card-admin">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-muted mb-1" style="font-size:.8rem">Categories</p>
            <h4 class="fw-bold mb-0">{{ $stats['categories'] }}</h4>
          </div>
          <i class="fa-solid fa-tags text-warning fs-3"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="card-admin">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-muted mb-1" style="font-size:.8rem">Contact Messages</p>
            <h4 class="fw-bold mb-0">{{ $stats['contact_messages'] }}</h4>
          </div>
          <i class="fa-solid fa-envelope text-info fs-3"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="card-admin">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-muted mb-1" style="font-size:.8rem">Unread Messages</p>
            <h4 class="fw-bold mb-0">{{ $stats['unread_messages'] }}</h4>
          </div>
          <i class="fa-solid fa-envelope-open-text text-danger fs-3"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="card-admin">
    <h6 class="fw-bold mb-3">Recent Messages</h6>
    <div class="table-responsive">
      <table class="table table-admin">
        <thead>
          <tr><th>Name</th><th>Email</th><th>Topic</th><th>Status</th></tr>
        </thead>
        <tbody>
          @forelse($recentMessages as $message)
            <tr>
              <td>{{ $message->first_name }} {{ $message->last_name }}</td>
              <td>{{ $message->email }}</td>
              <td>{{ $message->topic }}</td>
              <td>
                @if($message->is_read)
                  <span class="badge bg-secondary">Read</span>
                @else
                  <span class="badge bg-primary">New</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-3">No messages yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
