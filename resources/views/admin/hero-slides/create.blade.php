@extends('layouts.admin')

@section('title', 'Add Hero Slide')
@section('page-title', 'Add Hero Slide')

@section('content')
  <div class="card-admin" style="max-width:760px">
    <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('admin.hero-slides._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Save Slide</button>
    </form>
  </div>
@endsection
