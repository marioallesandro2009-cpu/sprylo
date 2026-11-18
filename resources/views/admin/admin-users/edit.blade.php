@extends('layouts.admin')

@section('title', 'Edit Admin')
@section('page-title', 'Edit Admin')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.admin-users.update', $adminUser) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.admin-users._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update Admin</button>
      <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary mt-2 ms-1">Cancel</a>
    </form>
  </div>
@endsection