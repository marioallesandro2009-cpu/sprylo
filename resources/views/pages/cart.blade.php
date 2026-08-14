@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Cart — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.cart-head')
    @if($cartItems->isEmpty())
        @include('partials.cart-empty')
    @else
        @include('partials.cart-body')
    @endif
@endsection
