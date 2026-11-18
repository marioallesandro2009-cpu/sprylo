@extends('layouts.admin')

@section('title', 'Edit Voucher Rule')
@section('page-title', 'Edit Voucher Rule')

@section('content')
  <div class="card-admin" style="max-width:720px">
    <form action="{{ route('admin.voucher-rules.update', $rule) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.voucher-rules._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Update Rule</button>
    </form>
  </div>
@endsection
