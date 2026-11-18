@extends('layouts.admin')

@section('title', 'Edit Page SEO')
@section('page-title', 'Edit Page SEO')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.page-meta.update', $pageMeta) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.page-meta._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update</button>
    </form>
  </div>
@endsection
