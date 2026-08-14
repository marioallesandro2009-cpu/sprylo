@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Shop — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.shop-head')
    @include('partials.shop-results')
    @include('partials.brand-strip', ['brands' => $brands])
@endsection
