@extends('layouts.admin')

@section('title', 'Message from '.$message->first_name)
@section('page-title', 'Message Detail')

@section('content')

  <div class="mb-3">
    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to inbox</a>
  </div>

  <div class="card-admin" style="max-width:720px">
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <div class="text-muted small">Name</div>
        <div class="fw-semibold">{{ $message->first_name }} {{ $message->last_name }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Email</div>
        <div class="fw-semibold"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Phone</div>
        <div class="fw-semibold">{{ $message->phone ?: '—' }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Topic</div>
        <div class="fw-semibold">{{ $message->topic ?: '—' }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Order number</div>
        <div class="fw-semibold">{{ $message->order_number ?: '—' }}</div>
      </div>
      <div class="col-md-6">
        <div class="text-muted small">Received</div>
        <div class="fw-semibold">{{ $message->created_at->format('d M Y, H:i') }}</div>
      </div>
    </div>

    <div class="text-muted small mb-1">Message</div>
    <p class="mb-0" style="white-space: pre-line">{{ $message->message }}</p>
  </div>

  <div class="mt-3">
    <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete this message?');">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash me-1"></i> Delete message</button>
    </form>
  </div>

@endsection
