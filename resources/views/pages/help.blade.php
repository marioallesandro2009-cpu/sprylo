@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Help Centre — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.help-head')
    @include('partials.help-faqs')
@endsection
