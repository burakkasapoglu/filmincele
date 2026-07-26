@props(['title' => null, 'description' => null, 'image' => null, 'type' => 'website', 'canonical' => null])

@php
$title = $title ?? 'Filmincele — Ruh haline göre film ve dizi keşfet';
$description = $description ?? 'Film ve dizi keşfet, puanla, liste oluştur. Ruh haline göre kişiselleştirilmiş öneriler al.';
$image = $image ?? asset('build/assets/app-logo.png');
$canonical = $canonical ?? url()->current();
@endphp

<meta name="description" content="{{ $description }}">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="{{ $canonical }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="Filmincele">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ url()->current() }}">
@if($image)<meta property="og:image" content="{{ $image }}">@endif
<meta property="og:locale" content="tr_TR">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
@if($image)<meta name="twitter:image" content="{{ $image }}">@endif
