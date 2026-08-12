@extends('layouts.admin')

@section('title', 'Add Category')
@section('page-title', 'Add Category')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('admin.categories._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save Category</button>
    </form>
  </div>
@endsection
