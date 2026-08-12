@extends('layouts.admin')

@section('title', 'Add Review')
@section('page-title', 'Add Review')

@section('content')
  <div class="card-admin" style="max-width:560px">
    <form action="{{ route('admin.reviews.store') }}" method="POST">
      @csrf
      @include('admin.reviews._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save Review</button>
    </form>
  </div>
@endsection
