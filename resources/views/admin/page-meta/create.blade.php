@extends('layouts.admin')

@section('title', 'Add Page SEO')
@section('page-title', 'Add Page SEO')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.page-meta.store') }}" method="POST">
      @csrf
      @include('admin.page-meta._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save</button>
    </form>
  </div>
@endsection
