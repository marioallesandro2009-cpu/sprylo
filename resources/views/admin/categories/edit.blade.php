@extends('layouts.admin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Category')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      @include('admin.categories._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update Category</button>
    </form>
  </div>
@endsection
