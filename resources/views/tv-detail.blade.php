@extends('layouts.app')
@section('title', 'Dizi — Filmincele')

@section('content')
<livewire:tv-detail :tmdb-id="$tmdbId" />
@endsection
