@extends('layouts.admin')

@section('title', 'Add Brand')
@section('page-title', 'Add Brand')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('admin.brands._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save Brand</button>
    </form>
  </div>
@endsection
