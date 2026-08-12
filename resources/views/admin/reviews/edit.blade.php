@extends('layouts.admin')

@section('title', 'Edit Review')
@section('page-title', 'Edit Review')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.reviews._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update Review</button>
    </form>
  </div>
@endsection
