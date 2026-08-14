@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Checkout — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.checkout-head')
    @include('partials.checkout-body')
@endsection
