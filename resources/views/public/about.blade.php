@extends('layouts.lasles-public')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $img = fn (string $file) => lasles_img($file);
    $content = is_array($content ?? null) ? $content : [];
@endphp

@include('partials.landing.lasles.about-hero', ['content' => $content, 'img' => $img, 'isRtl' => $isRtl])
@include('partials.landing.lasles.about-story', ['content' => $content, 'img' => $img])
@include('partials.landing.lasles.about-why', ['content' => $content])
@include('partials.landing.lasles.about-loop', ['content' => $content])
@include('partials.landing.lasles.about-cta', ['content' => $content])
@endsection
