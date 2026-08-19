@extends('layouts.admin')

@section('title', 'Edit Promo Banner')
@section('page-title', 'Edit Promo Banner')

@section('content')
  <div class="card-admin" style="max-width:680px">
    <form action="{{ route('admin.promo-banners.update', $banner) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.promo-banners._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Update Banner</button>
    </form>
  </div>
@endsection
