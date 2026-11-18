@php
  $catNames = collect($categories ?? [])->pluck('name')->map(fn ($n) => mb_strtolower($n))->values();
  $catList = $catNames->count() > 2
    ? $catNames->slice(0, -1)->implode(', ').', and '.$catNames->last()
    : $catNames->implode(' and ');
@endphp
<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">{{ $ui('ui_crumb_home') }}</a> <span class="sep">›</span> <span>{{ $ui('ui_shop_crumb') }}</span></div>
    <h1>{{ $settings['shop_head_title'] ?? 'All Products' }}</h1>
    <p>{{ count($products) }} products{{ $catNames->isNotEmpty() ? ' across '.$catList : '' }}. {{ $settings['shop_head_text'] ?? 'Use the filters on the left to narrow down by brand, price, rating, or availability.' }}</p>
  </div>
</section>