@extends('layouts.admin')

@section('title', 'Page Texts')
@section('page-title', 'Page Texts')

@section('content')

  <form action="{{ route('admin.ui-texts.update') }}" method="POST">
    @csrf @method('PUT')

    <p class="text-muted small mb-3">
      Every label, heading and paragraph shown on the storefront. Placeholders in curly braces (for example <code>{count}</code>)
      are filled in automatically &mdash; keep them in the text where the number or name should appear.
    </p>

    @foreach($groups as $group => $items)
      <div class="card-admin mb-3">
        <h6 class="fw-bold mb-3">{{ \Illuminate\Support\Str::headline($group) }}</h6>
        <div class="row g-3">
          @foreach($items as $item)
            @php
              $label = \Illuminate\Support\Str::headline(substr($item->key, strlen('ui_'.$group.'_')) ?: $item->key);
              $value = (string) $item->value;
              $rows = min(8, max(1, (int) ceil(strlen($value) / 60) + substr_count($value, "\n")));
              preg_match_all('/\{(\w+)\}/', $value, $found);
            @endphp
            <div class="col-md-6">
              <label class="form-label small fw-semibold">{{ $label }}</label>
              <textarea name="texts[{{ $item->key }}]" class="form-control" rows="{{ $rows }}">{{ old('texts.'.$item->key, $value) }}</textarea>
              @if(!empty($found[0]))
                <div class="form-text">Placeholders: {{ implode(', ', array_unique($found[0])) }}</div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach

    <button type="submit" class="btn btn-admin-primary">Save Texts</button>
  </form>

@endsection
