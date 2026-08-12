@extends('layouts.admin')

@section('title', 'Edit Brand')
@section('page-title', 'Edit Brand')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      @include('admin.brands._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update Brand</button>
    </form>
  </div>
@endsection
