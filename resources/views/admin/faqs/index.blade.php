@extends('layouts.admin')

@section('title', 'FAQs')
@section('page-title', 'FAQs')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add FAQ</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>#</th><th>Question</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($faqs as $faq)
            <tr>
              <td class="text-muted small">{{ $faq->sort_order }}</td>
              <td class="fw-semibold">{{ $faq->question }}</td>
              <td class="text-end">
                <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline" data-confirm="Delete this FAQ?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted py-4">No FAQs yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
