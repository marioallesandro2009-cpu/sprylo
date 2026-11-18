@extends('layouts.admin')

@section('title', 'Edit Menu Link')
@section('page-title', 'Edit Menu Link')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.nav-links.update', $link) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.nav-links._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update Link</button>
    </form>
  </div>
@endsection
