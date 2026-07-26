@extends('layouts.app')
@section('title', 'Film Detayı — Filmincele')

@section('content')
<livewire:movie-detail :tmdb-id="$tmdbId" />
@endsection
