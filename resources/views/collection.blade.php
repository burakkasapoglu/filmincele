@extends('layouts.app')
@section('title', $collection['name'] . ' — Filmincele')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="text-center mb-10">
        <span class="text-6xl block mb-3">{{ $collection['emoji'] }}</span>
        <h1 class="text-3xl font-bold text-white">{{ $collection['name'] }}</h1>
        <p class="text-gray-400 mt-2">{{ $collection['description'] }}</p>
    </div>

    @php $tmdb = app(\App\Services\TmdbService::class); @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @foreach($collection['movies'] as $tmdbId)
            @php $movie = $tmdb->getMovieDetails($tmdbId); @endphp
            @if($movie)
                <a href="{{ url('/film/' . $tmdbId . '-' . \Illuminate\Support\Str::slug($movie['title'] ?? '')) }}" class="group block">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        @if($movie['poster_path'])
                            <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                 alt="{{ $movie['title'] }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">🎬</div>
                        @endif
                        <div class="absolute top-2 left-2 px-2 py-0.5 bg-black/60 rounded text-xs text-white">{{ $loop->iteration }}</div>
                    </div>
                    <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">{{ $movie['title'] }}</h3>
                    <p class="text-gray-500 text-xs">{{ substr($movie['release_date'] ?? '—', 0, 4) }} · ★ {{ number_format($movie['vote_average'] ?? 0, 1) }}</p>
                </a>
            @endif
        @endforeach
    </div>

    <div class="mt-12 pt-6 border-t border-gray-800">
        <h3 class="text-white font-semibold mb-4">Diğer Koleksiyonlar</h3>
        <div class="flex flex-wrap gap-3">
            @foreach(config('collections') as $s => $c)
                <a href="{{ url('/koleksiyon/' . $s) }}" class="px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl text-sm text-white hover:border-rose-600 transition {{ $s === $slug ? 'border-rose-600 bg-rose-600/10 text-rose-400' : '' }}">
                    {{ $c['emoji'] }} {{ $c['name'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
