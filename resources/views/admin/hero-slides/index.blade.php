@extends('layouts.admin')

@section('title', 'Hero Slides')
@section('page-title', 'Hero Slides')

@section('content')

  <p class="text-muted small mb-3">These make up the homepage bento grid — one big featured card plus any number of small cards. Add or remove slides freely; the layout adjusts automatically.</p>

  <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-admin-primary"><i class="fa-solid fa-plus me-1"></i> Add Slide</a>
  </div>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table table-admin align-middle">
        <thead>
          <tr>
            <th>#</th><th></th><th>Heading</th><th>Size</th><th>Theme</th><th>Status</th><th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($slides as $slide)
            <tr>
              <td class="text-muted small">{{ $slide->sort_order }}</td>
              <td><img src="{{ $slide->image }}" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:8px" /></td>
              <td>
                <div class="fw-semibold">{{ $slide->heading }}</div>
                <div class="text-muted small">{{ $slide->eyebrow }}</div>
              </td>
              <td class="text-capitalize">{{ $slide->size }}</td>
              <td class="text-capitalize">{{ \App\Models\HeroSlide::THEMES[$slide->theme] ?? $slide->theme }}</td>
              <td>
                @if($slide->is_active)
                  <span class="badge text-bg-success">Active</span>
                @else
                  <span class="badge text-bg-secondary">Hidden</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.hero-slides.edit', $slide) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>
                <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this slide?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No hero slides yet — add one to populate the homepage hero.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
