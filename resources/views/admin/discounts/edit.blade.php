@extends('layouts.admin')

@section('title', $flash ? 'Ubah Flash Sale' : 'Ubah Diskon')
@section('page-title', $flash ? 'Ubah Flash Sale' : 'Ubah Diskon')

@section('content')
  <div class="card-admin" style="max-width:760px">
    <form action="{{ route($routePrefix.'.update', $discount) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.discounts._form')
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-admin-primary">Simpan perubahan</button>
        <a href="{{ route($routePrefix.'.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
@endsection
