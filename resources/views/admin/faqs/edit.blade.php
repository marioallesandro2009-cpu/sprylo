@extends('layouts.admin')

@section('title', 'Edit FAQ')
@section('page-title', 'Edit FAQ')

@section('content')
  <div class="card-admin" style="max-width:640px">
    <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
      @csrf @method('PUT')
      @include('admin.faqs._form')
      <button type="submit" class="btn btn-admin-primary mt-2">Update FAQ</button>
    </form>
  </div>
@endsection
