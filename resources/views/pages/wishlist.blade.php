@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Wishlist — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.wishlist-head')
    @if($products->isEmpty())
        @include('partials.wishlist-empty')
    @else
        @include('partials.wishlist-grid')
    @endif
@endsection
