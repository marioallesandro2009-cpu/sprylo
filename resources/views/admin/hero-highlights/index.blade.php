@extends('layouts.admin')

@section('title', 'Hero Highlights')
@section('page-title', 'Hero Highlights')

@section('content')

  <p class="text-muted small mb-3">These are the 5 smaller cards in the homepage hero grid (the big featured card is edited under Site Settings → Homepage Hero Banner).</p>

  <form action="{{ route('admin.hero-highlights.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    @foreach(\App\Models\HeroHighlight::POSITIONS as $position => $label)
      @php $slide = $slides->get($position); @endphp
      <div class="card-admin mb-3">
        <h6 class="fw-bold mb-3">{{ $label }}</h6>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Eyebrow label</label>
            <input type="text" name="{{ $position }}[eyebrow]" class="form-control" value="{{ old("{$position}.eyebrow", $slide->eyebrow ?? '') }}" required />
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Heading</label>
            <input type="text" name="{{ $position }}[heading]" class="form-control" value="{{ old("{$position}.heading", $slide->heading ?? '') }}" required />
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">Linked product</label>
            <select name="{{ $position }}[product_id]" class="form-select">
              <option value="">None (links to shop)</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}" @selected(old("{$position}.product_id", $slide->product_id ?? '') == $product->id)>{{ $product->name }}</option>
              @endforeach
            </select>
          </div>
          @if($slide?->image)
            <div class="col-12">
              <img src="{{ $slide->image }}" alt="" style="width:70px;height:70px;object-fit:cover;border-radius:8px" />
            </div>
          @endif
          <div class="col-12">
            <label class="form-label small fw-semibold">{{ $slide?->image ? 'Replace image' : 'Image' }}</label>
            <input type="file" name="{{ $position }}[image]" class="form-control" accept="image/*" />
          </div>
        </div>
      </div>
    @endforeach

    <button type="submit" class="btn btn-admin-primary">Save Hero Highlights</button>
  </form>

@endsection
