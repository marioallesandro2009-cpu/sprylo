@extends('layouts.admin')

@section('title', 'Add Footer Link')
@section('page-title', 'Add Footer Link')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.footer-links.store') }}" method="POST">
      @csrf
      @include('admin.footer-links._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save Link</button>
    </form>
  </div>
@endsection
