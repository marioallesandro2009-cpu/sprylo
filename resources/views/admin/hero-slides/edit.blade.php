@extends('layouts.admin')

@section('title', 'Edit Hero Slide')
@section('page-title', 'Edit Hero Slide')

@section('content')
  <div class="card-admin" style="max-width:760px">
    <form action="{{ route('admin.hero-slides.update', $slide) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      @include('admin.hero-slides._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Update Slide</button>
    </form>
  </div>
@endsection
