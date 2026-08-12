@extends('layouts.app')

@section('title', $pageMeta->title ?? ($settings['site_name'] ?? 'Sprylo'))
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

  @if(($settings['section_categories_enabled'] ?? '1') !== '0')
    @include('partials.categories')
  @endif

  @if(($settings['section_just_for_you_enabled'] ?? '1') !== '0')
    @include('partials.just-for-you')
  @endif

  @if(($settings['section_brands_enabled'] ?? '1') !== '0')
    @include('partials.brand-strip', ['brands' => $brands])
  @endif

  @if(($settings['section_newsletter_enabled'] ?? '1') !== '0')
    @include('partials.newsletter')
  @endif
@endsection
