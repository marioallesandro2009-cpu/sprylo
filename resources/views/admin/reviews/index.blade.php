@extends('layouts.admin')

@section('title', 'Reviews')
@section('page-title', 'Reviews')

@section('content')

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.reviews.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Review</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr><th>Product</th><th>Reviewer</th><th>Rating</th><th>Comment</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
          @forelse($reviews as $review)
            <tr>
              <td class="fw-semibold">{{ $review->product->name ?? '—' }}</td>
              <td>{{ $review->name }}</td>
              <td><span class="text-warning">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span></td>
              <td class="text-muted small">{{ \Illuminate\Support\Str::limit($review->comment, 60) }}</td>
              <td class="text-end">
                <a href="{{ route('admin.reviews.edit', $review) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" data-confirm="Delete this review?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No reviews yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
