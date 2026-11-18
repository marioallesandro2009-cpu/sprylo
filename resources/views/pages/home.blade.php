@extends('layouts.app')

@section('title', $pageMeta->title ?? ($settings['site_name'] ?? 'solis'))
@section('meta_description', $pageMeta->meta_description ?? ($settings['site_description'] ?? ''))

@section('content')
  @if(($settings['section_hero_enabled'] ?? '1') !== '0')
    @include('partials.hero')
  @endif

  @if(($settings['section_trending_enabled'] ?? '1') !== '0')
    @include('partials.trending-products')
  @endif

  @if(($settings['section_discounts_enabled'] ?? '1') !== '0')
    @include('partials.discount-banners')
  @endif

  @if(($settings['section_just_for_you_enabled'] ?? '1') !== '0')
    @include('partials.just-for-you')
  @endif
@endsection
