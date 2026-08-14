@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' — '.($settings['site_name'] ?? 'Sprylo'))

@section('content')
    @include('partials.checkout-confirmation')
@endsection
