@extends('layouts.admin')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('content')
  <div class="card-admin" style="max-width:720px">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('admin.products._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save Product</button>
    </form>
  </div>
@endsection
