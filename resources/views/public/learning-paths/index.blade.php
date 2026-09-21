@extends('layouts.lasles-public')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $img = fn (string $file) => lasles_img($file);
@endphp

@include('partials.landing.lasles.paths-hero', ['img' => $img, 'isRtl' => $isRtl])
@include('partials.landing.lasles.paths-guide')
@include('partials.landing.lasles.paths-catalog', ['paths' => $paths])
@include('partials.landing.lasles.paths-cta')
@endsection
