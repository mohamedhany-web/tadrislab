@extends('layouts.lasles-public')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $img = fn (string $file) => asset('img/lasles/'.$file);
    $steps = [
        ['num' => '01', 'key' => 'diagnose'],
        ['num' => '02', 'key' => 'access'],
        ['num' => '03', 'key' => 'develop'],
        ['num' => '04', 'key' => 'measure'],
    ];
@endphp

@include('partials.landing.lasles.path-page-hero')
@include('partials.landing.lasles.path-page-steps', ['steps' => $steps])
@include('partials.landing.lasles.path-page-promise')
@endsection
