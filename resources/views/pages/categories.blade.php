@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Categories — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.categories-head')
    @include('partials.categories-grid')
@endsection
