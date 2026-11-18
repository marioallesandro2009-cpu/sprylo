@extends('layouts.app')

@section('title', $pageMeta->title ?? ('About Us — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.about')
    @include('partials.history')
    @include('partials.hours')
@endsection
