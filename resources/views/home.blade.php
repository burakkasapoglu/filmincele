@extends('layouts.app')
@section('title', 'Ruh haline göre keşfet')

@section('content')
<div class="min-h-screen bg-gray-950">
    {{-- Hero --}}
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-rose-900/20 via-gray-950 to-indigo-900/20"></div>
        <div class="relative max-w-7xl mx-auto px-4 pt-20 pb-12 text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-4 tracking-tight">
                Ruh haline göre<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-purple-400">film & dizi keşfet</span>
            </h1>
            <p class="text-lg text-gray-400 max-w-2xl mx-auto mb-8">
                Nasıl hissediyorsun? Sana özel film önerileri için bir ruh hali seç, keşfetmeye başla.
            </p>
        </div>
    </div>

    {{-- Mood Selector --}}
    <livewire:mood-selector />

    {{-- Random Movie --}}
    <div class="max-w-7xl mx-auto px-4 -mt-4">
        <livewire:random-movie />
    </div>

    {{-- Personalized Recommendations --}}
    @auth
        @php
            $recService = app(\App\Services\RecommendationService::class);
            $recommendations = $recService->getForUser(Auth::user(), 12);
        @endphp
        @if(!empty($recommendations) && Auth::user()->ratings()->count() >= 2)
            <div class="max-w-7xl mx-auto px-4 py-8">
                <h2 class="text-xl font-bold text-white mb-4">🎯 Senin İçin Öneriler</h2>
                <p class="text-gray-500 text-sm -mt-3 mb-5">Puanladığın filmlere göre seçildi</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($recommendations as $movie)
                        <a href="{{ film_url($movie['id'], $movie['title'] ?? '') }}" class="group block">
                            <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                                @if($movie['poster_path'] ?? null)
                                    <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}" loading="lazy"
                                         class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">🎬</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                                    <div class="flex items-center gap-1 text-yellow-400 text-sm">★ {{ number_format($movie['vote_average'] ?? 0, 1) }}</div>
                                </div>
                            </div>
                            <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">{{ $movie['title'] }}</h3>
                            <p class="text-gray-500 text-xs">{{ substr($movie['release_date'] ?? '—', 0, 4) }} · ★ {{ number_format($movie['vote_average'] ?? 0, 1) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endauth

    {{-- Today in Cinema --}}
    <livewire:today-in-cinema />

    {{-- Trending Section --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-white mb-6">🔥 Şu Anda Trendler</h2>
        @php
            $tmdb = app(\App\Services\TmdbService::class);
            $trending = $tmdb->getTrending();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach(array_slice($trending, 0, 12) as $movie)
                <a href="{{ url('/film/' . $movie['id'] . '-' . \Illuminate\Support\Str::slug($movie['title'] ?? $movie['name'] ?? '')) }}" class="group block">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        @if($movie['poster_path'])
                            <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                 alt="{{ $movie['title'] ?? $movie['name'] ?? '' }}"
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
                        {{ $movie['title'] ?? $movie['name'] ?? '' }}
                    </h3>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Latest Blog Posts --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">📝 Blog</h2>
            <a href="{{ url('/blog') }}" class="text-sm text-rose-400 hover:text-rose-300 transition">Tüm Yazılar →</a>
        </div>
        @php $latestPosts = \App\Models\Post::where('is_published', true)->latest()->take(6)->get(); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($latestPosts as $post)
                <a href="{{ url('/blog/' . $post->slug) }}" class="group bg-gray-900 rounded-2xl border border-gray-800/50 overflow-hidden hover:border-gray-700 transition">
                    <div class="aspect-[16/9] bg-gray-800 overflow-hidden">
                        @if($post->image_url)
                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-3xl text-gray-700">📝</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-rose-600/10 text-rose-400 text-[10px] rounded-full">{{ $post->category }}</span>
                            <span class="text-gray-600 text-[10px]">{{ $post->read_time }} dk</span>
                        </div>
                        <h3 class="text-white font-medium text-sm line-clamp-2 group-hover:text-rose-400 transition">{{ $post->title }}</h3>
                        <p class="text-gray-500 text-xs mt-1 line-clamp-2">{{ $post->excerpt }}</p>
                        <p class="text-gray-600 text-[10px] mt-2">{{ $post->published_at->format('d.m.Y') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
