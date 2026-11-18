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

    {{-- One standalone form per image; the ✕ buttons inside the product form point at these via form="…". --}}
    @foreach($product->images as $image)
      <form id="delete-image-{{ $image->id }}" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST" data-confirm="Remove this image?" hidden>
        @csrf @method('DELETE')
      </form>
    @endforeach
  </div>
@endsection
