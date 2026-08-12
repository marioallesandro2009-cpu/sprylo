@php $review = $review ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Product</label>
  <select name="product_id" class="form-select" required>
    <option value="">Select…</option>
    @foreach($products as $product)
      <option value="{{ $product->id }}" @selected(old('product_id', $review->product_id ?? '') == $product->id)>{{ $product->name }}</option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Reviewer name</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $review->name ?? '') }}" required />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Rating</label>
  <select name="rating" class="form-select" style="max-width:160px" required>
    @foreach([5, 4, 3, 2, 1] as $stars)
      <option value="{{ $stars }}" @selected(old('rating', $review->rating ?? 5) == $stars)>{{ str_repeat('★', $stars) }}{{ str_repeat('☆', 5 - $stars) }}</option>
    @endforeach
  </select>
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Comment</label>
  <textarea name="comment" rows="4" class="form-control" required>{{ old('comment', $review->comment ?? '') }}</textarea>
</div>
