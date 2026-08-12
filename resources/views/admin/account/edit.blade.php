@extends('layouts.admin')

@section('title', 'My Account')
@section('page-title', 'My Account')

@section('content')

  <div class="card-admin mb-3" style="max-width:560px">
    <h6 class="fw-bold mb-3">Account Details</h6>
    <form action="{{ route('admin.account.update') }}" method="POST">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label small fw-semibold">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required />
      </div>
      <div class="mb-3">
        <label class="form-label small fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required />
      </div>
      <button type="submit" class="btn btn-admin-primary">Update Details</button>
    </form>
  </div>

  <div class="card-admin" style="max-width:560px">
    <h6 class="fw-bold mb-3">Change Password</h6>
    <form action="{{ route('admin.account.password') }}" method="POST">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label small fw-semibold">Current password</label>
        <input type="password" name="current_password" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label small fw-semibold">New password</label>
        <input type="password" name="password" class="form-control" required />
        <div class="form-text">At least 8 characters.</div>
      </div>
      <div class="mb-3">
        <label class="form-label small fw-semibold">Confirm new password</label>
        <input type="password" name="password_confirmation" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-admin-primary">Update Password</button>
    </form>
  </div>

@endsection
