@extends('layouts.admin')

@section('title', 'Edit SEO')
@section('page-title', 'Edit SEO — '.$pageMeta->page_slug)

@section('content')
  <div class="card-admin" style="max-width:720px">
    <form action="{{ route('admin.page-meta.update', $pageMeta) }}" method="POST">
      @csrf @method('PUT')

      <div class="mb-3">
        <label class="form-label small fw-semibold">Page</label>
        <input type="text" class="form-control" value="{{ $pageMeta->page_slug }}" disabled />
      </div>

      <div class="mb-3">
        <label class="form-label small fw-semibold">Title</label>
        <input type="text" name="title" class="form-control" maxlength="255" value="{{ old('title', $pageMeta->title) }}" />
        <div class="form-text">Ideal sekitar 60 karakter.</div>
      </div>

      <div class="mb-3">
        <label class="form-label small fw-semibold">Meta description</label>
        <textarea name="meta_description" rows="3" class="form-control" maxlength="500">{{ old('meta_description', $pageMeta->meta_description) }}</textarea>
        <div class="form-text">Ideal sekitar 150–160 karakter.</div>
      </div>

      <button type="submit" class="btn btn-admin-primary">Update</button>
      <a href="{{ route('admin.page-meta.index') }}" class="btn btn-outline-secondary ms-1">Cancel</a>
    </form>
  </div>
@endsection