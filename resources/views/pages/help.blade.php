@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Help Centre — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.help-head')
    @include('partials.help-faqs')
@endsection
