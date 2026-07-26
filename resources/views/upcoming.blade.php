@extends('layouts.app')
@section('title', 'Yakında — Filmincele')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-2">📅 Yakında Vizyonda</h1>
    <p class="text-gray-400 mb-8">Yakında sinemalarda olacak filmler</p>

    @php
        $tmdb = app(\App\Services\TmdbService::class);
        $movies = $tmdb->getUpcoming();
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @foreach($movies as $movie)
            <a href="{{ url('/film/' . $movie['id'] . '-' . \Illuminate\Support\Str::slug($movie['title'] ?? '')) }}" class="group block">
                <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                    @if($movie['poster_path'])
                        <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                             alt="{{ $movie['title'] }}"
                             class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                             loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">🎬</div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                        <div class="flex items-center gap-1 text-yellow-400 text-sm">
                            ★ {{ number_format($movie['vote_average'] ?? 0, 1) }}
                        </div>
                    </div>
                </div>
                <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">
                    {{ $movie['title'] }}
                </h3>
                <p class="text-gray-500 text-xs">
                    {{ isset($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->format('d.m.Y') : '—' }}
                </p>
            </a>
        @endforeach
    </div>

    @if(empty($movies))
        <div class="text-center py-20 text-gray-500">
            <p class="text-4xl mb-4">📅</p>
            <p>Yakında vizyona girecek içerik bulunamadı.</p>
        </div>
    @endif
</div>
@endsection
