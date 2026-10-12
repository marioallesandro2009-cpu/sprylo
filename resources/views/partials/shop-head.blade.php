@php
  $catNames = collect($categories ?? [])->pluck('name')->map(fn ($n) => mb_strtolower($n))->values();
  $catList = $catNames->count() > 2
    ? $catNames->slice(0, -1)->implode(', ').', and '.$catNames->last()
    : $catNames->implode(' and ');
@endphp
<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Shop · All products</span></div>
    <h1>{{ $settings['shop_head_title'] ?? 'All Products' }}</h1>
    <p>{{ count($products) }} products@if($catNames->isNotEmpty()) across {{ $catList }}@endif. {{ $settings['shop_head_text'] ?? 'Use the filters on the left to narrow down by brand, price, rating, or availability.' }}</p>
  </div>
</section>