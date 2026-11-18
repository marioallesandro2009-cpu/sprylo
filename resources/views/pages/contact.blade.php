@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Contact — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.contact-head')
    @include('partials.contact-body')
    @include('partials.contact-faq')
@endsection
