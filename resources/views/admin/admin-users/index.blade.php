@extends('layouts.admin')

@section('title', 'Admin Accounts')
@section('page-title', 'Admin Accounts')

@section('content')

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <p class="text-muted small mb-0">Hanya super admin yang bisa melihat dan mengelola halaman ini.</p>
    <a href="{{ route('admin.admin-users.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Admin</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @foreach($admins as $admin)
            <tr>
              <td class="fw-semibold">
                {{ $admin->name }}
                @if($admin->id === auth()->id())<span class="badge bg-light text-dark border ms-1">You</span>@endif
              </td>
              <td>{{ $admin->email }}</td>
              <td>
                @if($admin->role === 'super_admin')
                  <span class="badge bg-dark">Super Admin</span>
                @else
                  <span class="badge bg-primary">Admin</span>
                @endif
              </td>
              <td>
                @if($admin->isActive())
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-danger">Suspended</span>
                @endif
              </td>
              <td class="text-muted small">{{ $admin->created_at?->format('d M Y') }}</td>
              <td class="text-end">
                @if($admin->role === 'admin')
                  <a href="{{ route('admin.admin-users.edit', $admin) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fa-solid fa-pen"></i></a>

                  <form action="{{ route('admin.admin-users.status', $admin) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-warning">{{ $admin->isActive() ? 'Suspend' : 'Activate' }}</button>
                  </form>

                  <form action="{{ route('admin.admin-users.destroy', $admin) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this admin account?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                  </form>
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

@endsection