@extends('layouts.admin')

@section('title', 'Edit '.$noun)
@section('page-title', 'Edit '.$noun)

@section('content')
  <div class="card-admin" style="max-width:760px">
    <form action="{{ route($routePrefix.'.update', $discount) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.discounts._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Update {{ $noun }}</button>
    </form>
  </div>
@endsection
