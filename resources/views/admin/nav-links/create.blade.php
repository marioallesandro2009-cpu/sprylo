@extends('layouts.admin')

@section('title', 'Add Menu Link')
@section('page-title', 'Add Menu Link')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.nav-links.store') }}" method="POST">
      @csrf
      @include('admin.nav-links._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save Link</button>
    </form>
  </div>
@endsection
