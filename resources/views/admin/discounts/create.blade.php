@extends('layouts.admin')

@section('title', $flash ? 'Tambah Flash Sale' : 'Tambah Diskon')
@section('page-title', $flash ? 'Tambah Flash Sale' : 'Tambah Diskon')

@section('content')
  <div class="card-admin" style="max-width:760px">
    <form action="{{ route($routePrefix.'.store') }}" method="POST">
      @csrf
      @include('admin.discounts._form')
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-admin-primary">Simpan {{ $flash ? 'Flash Sale' : 'Diskon' }}</button>
        <a href="{{ route($routePrefix.'.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
@endsection
