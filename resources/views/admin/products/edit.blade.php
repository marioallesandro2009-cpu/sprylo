@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
  <div class="card-admin" style="max-width:720px">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      @include('admin.products._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update Product</button>
    </form>
  </div>
@endsection
