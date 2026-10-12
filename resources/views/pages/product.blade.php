@extends('layouts.app')

@section('title', $product->name.' — '.($settings['site_name'] ?? 'Sprylo'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description), 160))

@section('content')
    @include('partials.product-body')
@endsection

