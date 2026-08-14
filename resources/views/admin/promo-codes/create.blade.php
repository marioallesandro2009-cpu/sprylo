@extends('layouts.admin')

@section('title', 'Add Promo Code')
@section('page-title', 'Add Promo Code')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.promo-codes.store') }}" method="POST">
      @csrf
      @include('admin.promo-codes._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Save Promo Code</button>
    </form>
  </div>
@endsection
