@extends('layouts.admin')

@section('title', 'Add Promo Banner')
@section('page-title', 'Add Promo Banner')

@section('content')
  <div class="card-admin" style="max-width:680px">
    <form action="{{ route('admin.promo-banners.store') }}" method="POST">
      @csrf
      @include('admin.promo-banners._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Save Banner</button>
    </form>
  </div>
@endsection
