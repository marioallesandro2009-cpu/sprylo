@extends('layouts.admin')

@section('title', 'Add Voucher Rule')
@section('page-title', 'Add Voucher Rule')

@section('content')
  <div class="card-admin" style="max-width:720px">
    <form action="{{ route('admin.voucher-rules.store') }}" method="POST">
      @csrf
      @include('admin.voucher-rules._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Save Rule</button>
    </form>
  </div>
@endsection
