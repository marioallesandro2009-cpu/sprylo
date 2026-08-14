@extends('layouts.admin')

@section('title', 'Edit Promo Code')
@section('page-title', 'Edit Promo Code')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.promo-codes.update', $promoCode) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.promo-codes._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Update Promo Code</button>
    </form>
  </div>
@endsection
