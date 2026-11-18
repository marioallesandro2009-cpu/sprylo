@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Brands — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.brands-head')
    @include('partials.brands-grid')
@endsection
