@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' — '.($settings['site_name'] ?? 'solis'))

@section('content')
    @include('partials.checkout-confirmation')
@endsection
