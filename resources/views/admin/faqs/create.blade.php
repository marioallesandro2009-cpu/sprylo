@extends('layouts.admin')

@section('title', 'Add FAQ')
@section('page-title', 'Add FAQ')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.faqs.store') }}" method="POST">
      @csrf
      @include('admin.faqs._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Save FAQ</button>
    </form>
  </div>
@endsection
