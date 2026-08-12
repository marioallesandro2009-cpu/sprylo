@php $faq = $faq ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Question</label>
  <input type="text" name="question" class="form-control" value="{{ old('question', $faq->question ?? '') }}" required />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Answer</label>
  <textarea name="answer" rows="4" class="form-control" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Sort order</label>
  <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" style="max-width:120px" />
</div>
