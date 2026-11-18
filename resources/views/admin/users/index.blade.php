@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@push('styles')
  <style>
    @media (max-width: 767px) {
      .table-users thead { display: none; }
      .table-users, .table-users tbody, .table-users tr, .table-users td { display: block; width: 100%; }
      .table-users tr { border: 1px solid #eee; border-radius: 10px; margin-bottom: 14px; padding: 12px 14px; }
      .table-users td { border: none; padding: 6px 0; text-align: left !important; }
      .table-users td::before { content: attr(data-label); display: block; font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; color: #999; font-weight: 600; margin-bottom: 2px; }
      .table-users td.text-end { text-align: left !important; }
      .table-users td.text-end .btn { margin-bottom: 6px; }
    }
  </style>
@endpush

@section('content')

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin table-users align-middle">
        <thead>
          <tr><th>Name</th><th>Email</th><th>Status</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td data-label="Name" class="fw-semibold">{{ $user->name }}</td>
              <td data-label="Email">{{ $user->email }}</td>
              <td data-label="Status">
                @if($user->isBanned())
                  <span class="badge bg-danger">Banned</span>
                @elseif($user->isSuspended())
                  <span class="badge bg-warning text-dark">
                    Suspended
                    @if($user->suspended_until) until {{ $user->suspended_until->format('d M Y H:i') }} @endif
                  </span>
                @else
                  <span class="badge bg-success">Active</span>
                @endif
                @if($user->status_reason)
                  <div class="text-muted small mt-1">{{ $user->status_reason }}</div>
                @endif
              </td>
              <td data-label="Actions" class="text-end">
                @if($user->isActive())
                  <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $user->id }}">
                    <i class="fa-solid fa-hourglass-half"></i> Suspend
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#banModal{{ $user->id }}">
                    <i class="fa-solid fa-ban"></i> Ban
                  </button>
                @else
                  <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="d-inline" data-confirm="Reactivate this account?">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-check"></i> Reactivate</button>
                  </form>
                @endif
              </td>
            </tr>

          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No users yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @foreach($users as $user)
            <div class="modal fade" id="suspendModal{{ $user->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                      <h5 class="modal-title">Suspend {{ $user->name }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <input type="text" name="status_reason" class="form-control" maxlength="255" placeholder="e.g. Policy violation under review">
                      </div>
                      <div class="mb-1">
                        <label class="form-label">Suspend until (optional)</label>
                        <input type="datetime-local" name="suspended_until" class="form-control">
                        <div class="form-text">Leave blank to suspend indefinitely until manually reactivated.</div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-warning">Suspend Account</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                      <h5 class="modal-title">Ban {{ $user->name }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p class="text-muted small">Banning permanently blocks this account from logging in until it is manually reactivated.</p>
                      <div class="mb-1">
                        <label class="form-label">Reason (optional)</label>
                        <input type="text" name="status_reason" class="form-control" maxlength="255" placeholder="e.g. Security policy violation">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger">Ban Account</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
  @endforeach

@endsection