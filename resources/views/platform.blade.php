@extends('layouts.app')
@section('title', (isset($name) ? \Illuminate\Support\Str::title(str_replace('-', ' ', $name)) : 'Platform') . ' — Filmincele')

@section('content')
<livewire:platform-content :provider-id="$providerId" :name="$name ?? null" />
@endsection
