@extends('layouts.admin')

@section('title', 'Add Admin')
@section('page-title', 'Add Admin')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.admin-users.store') }}" method="POST">
      @csrf
      @include('admin.admin-users._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Create Admin</button>
      <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary mt-2 ms-1">Cancel</a>
    </form>
  </div>
@endsection