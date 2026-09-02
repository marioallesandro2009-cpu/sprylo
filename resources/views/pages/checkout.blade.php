@extends('layouts.app')

@section('title', $pageMeta->title ?? ('Checkout — '.($settings['site_name'] ?? 'Sprylo')))
@section('meta_description', $pageMeta->meta_description ?? '')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
    @include('partials.checkout-head')
    @include('partials.checkout-body')
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('assets/js/checkout-map.js') }}" defer></script>
    <script src="{{ asset('assets/js/checkout-shipping.js') }}" defer></script>
    <script src="{{ asset('assets/js/checkout-payment.js') }}" defer></script>
@endpush
