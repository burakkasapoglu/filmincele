@extends('layouts.app')
@section('title', 'Keşfet — Filmincele')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-6">🔍 Film & Dizi Keşfet</h1>

    @php $tmdb = app(\App\Services\TmdbService::class); @endphp

    {{-- Search Form --}}
    <form action="{{ url('/kesfet') }}" method="GET" class="mb-8">
        <div class="flex gap-3">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Film, dizi ara..."
                   class="flex-1 bg-gray-900 border border-gray-700 rounded-xl px-5 py-3 text-white placeholder-gray-500 focus:ring-rose-500 focus:border-rose-500">
            <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-500 text-white font-medium rounded-xl transition">
                Ara
            </button>
        </div>
    </form>

    {{-- Results --}}
    @if(request('q'))
        @php
            $results = $tmdb->searchMulti(request('q'));
        @endphp

        <p class="text-gray-400 mb-4">"{{ request('q') }}" için {{ count($results) }} sonuç bulundu.</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @foreach($results as $item)
                @php
                    $isTV = ($item['media_type'] ?? '') === 'tv';
                    $itemName = $isTV ? ($item['name'] ?? '') : ($item['title'] ?? '');
                    $itemDate = $isTV ? ($item['first_air_date'] ?? null) : ($item['release_date'] ?? null);
                    $itemUrl = $isTV ? dizi_url($item['id'], $itemName) : film_url($item['id'], $itemName);
                @endphp
                <a href="{{ $itemUrl }}" class="group block">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        @if($item['poster_path'] ?? null)
                            <img src="https://image.tmdb.org/t/p/w342{{ $item['poster_path'] }}"
                                 alt="{{ $itemName }}"
                                 class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">{{ $isTV ? '📺' : '🎬' }}</div>
                        @endif
                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-black/60 rounded text-[10px] text-white">{{ $isTV ? 'Dizi' : 'Film' }}</span>
                    </div>
                    <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">
                        {{ $itemName }}
                    </h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                        <span>{{ $itemDate ? substr($itemDate, 0, 4) : '—' }}</span>
                        <span>★ {{ number_format($item['vote_average'] ?? 0, 1) }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        @if(empty($results))
            <div class="text-center py-20 text-gray-500">
                <p class="text-4xl mb-4">🔍</p>
                <p>Aramanızla eşleşen içerik bulunamadı.</p>
            </div>
        @endif
    @else
        {{-- Popular movies --}}
        <h2 class="text-xl font-semibold text-white mb-4">Popüler Film ve Diziler</h2>
        @php
            $popular = $tmdb->getPopularMovies();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @foreach($popular as $movie)
                <a href="{{ film_url($movie['id'], $movie['title'] ?? '') }}" class="group block">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        @if($movie['poster_path'])
                            <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                 alt="{{ $movie['title'] }}"
                                 class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">🎬</div>
                        @endif
                    </div>
                    <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">
                        {{ $movie['title'] }}
                    </h3>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
