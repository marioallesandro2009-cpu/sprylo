@extends('layouts.admin')

@section('title', 'Add '.$noun)
@section('page-title', 'Add '.$noun)

@section('content')
  <div class="card-admin" style="max-width:760px">
    <form action="{{ route($routePrefix.'.store') }}" method="POST">
      @csrf
      @include('admin.discounts._form')
      <button type="submit" class="btn btn-admin-primary mt-3">Save {{ $noun }}</button>
    </form>
  </div>
@endsection
