@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Privacy Policy — '.($settings['site_name'] ?? 'solis')))
@section('meta_description', $pageMeta->meta_description ?? '')

@section('content')
    @include('partials.privacy-policy')
@endsection
