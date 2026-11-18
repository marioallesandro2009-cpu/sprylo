@php $pageMeta = $pageMeta ?? null; @endphp

<div class="mb-3">
  <label class="form-label small fw-semibold">Page</label>
  <input type="text" name="page_slug" list="known-pages" class="form-control" value="{{ old('page_slug', $pageMeta->page_slug ?? '') }}" required />
  @isset($missing)
    <datalist id="known-pages">
      @foreach($missing as $slug)
        <option value="{{ $slug }}"></option>
      @endforeach
    </datalist>
    <div class="form-text">Pages without an entry yet: {{ count($missing) ? implode(', ', $missing) : 'none' }}.</div>
  @endisset
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Title</label>
  <input type="text" name="title" class="form-control" value="{{ old('title', $pageMeta->title ?? '') }}" maxlength="255" />
</div>

<div class="mb-3">
  <label class="form-label small fw-semibold">Meta description</label>
  <textarea name="meta_description" class="form-control" rows="3" maxlength="500">{{ old('meta_description', $pageMeta->meta_description ?? '') }}</textarea>
</div>
