@extends('layouts.app')
@section('title', 'Ruh haline göre keşfet')

@section('content')
<div class="min-h-screen bg-gray-950">
    {{-- Hero --}}
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-900/15 via-gray-950 to-gray-900/20"></div>
        <div class="absolute inset-0 cinema-glow"></div>
        <x-falling-pattern />
        <div class="relative max-w-7xl mx-auto px-4 pt-20 pb-12 text-center">
            <h1 class="animate-fade-up text-5xl md:text-7xl font-extrabold text-white mb-4 tracking-tight">
                Ruh haline göre<br>
                <span class="gold-shimmer">film & dizi keşfet</span>
            </h1>
            <p class="animate-fade-up-delay-1 text-lg text-gray-400 max-w-2xl mx-auto mb-8">
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
        @if(!empty($recommendations))
            <div class="max-w-7xl mx-auto px-4 py-8">
                <h2 class="text-xl font-bold text-white mb-1">🎯 Senin İçin Öneriler</h2>
                <p class="text-gray-400 text-sm mb-5">
                    @if(Auth::user()->ratings()->count() > 0)
                        Puanların ve listen temel alınarak seçildi
                    @else
                        Başlamak için popüler seçkiler — birkaç film puanla, sana özel öneriler gelsin!
                    @endif
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($recommendations as $movie)
                        <a href="{{ film_url($movie['id'], $movie['title'] ?? '') }}" class="group block">
                            <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                                @if($movie['poster_path'] ?? null)
                                    <img src="https://image.tmdb.org/t/p/w185{{ $movie['poster_path'] }}" loading="lazy" alt="{{ $movie['title'] }}"
                                         srcset="https://image.tmdb.org/t/p/w185{{ $movie['poster_path'] }} 1x, https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }} 2x"
                                         class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl text-gray-400">🎬</div>
                                @endif
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-0.5 bg-black/60 backdrop-blur-sm rounded-lg text-xs font-semibold text-white">★ {{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                                    <p class="text-white text-xs line-clamp-2">{{ $movie['overview'] ?? '' }}</p>
                                </div>
                            </div>
                            <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">{{ $movie['title'] }}</h3>
                            <p class="text-gray-400 text-xs">{{ substr($movie['release_date'] ?? '—', 0, 4) }}</p>
                            @if($movie['_reason'] ?? null)
                                <p class="text-rose-400/60 text-[10px] mt-0.5 truncate">{{ $movie['_reason'] }}</p>
                            @endif
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
            $trending = \Illuminate\Support\Facades\Cache::remember('home:trending', 1800, fn () => app(\App\Services\TmdbService::class)->getTrending());
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach(array_slice($trending, 0, 12) as $movie)
                <a href="{{ url('/film/' . $movie['id'] . '-' . \Illuminate\Support\Str::slug($movie['title'] ?? $movie['name'] ?? '')) }}" class="group block cinema-card">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        @if($movie['poster_path'])
                            <img src="https://image.tmdb.org/t/p/w185{{ $movie['poster_path'] }}"
                                 srcset="https://image.tmdb.org/t/p/w185{{ $movie['poster_path'] }} 1x, https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }} 2x"
                                 alt="{{ $movie['title'] ?? $movie['name'] ?? '' }}"
                                 class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-400">🎬</div>
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
            <a href="{{ url('/blog') }}" class="text-sm text-rose-400 hover:text-rose-300 underline underline-offset-4 decoration-rose-400/60 transition">Tüm Yazılar →</a>
        </div>
        @php $latestPosts = \Illuminate\Support\Facades\Cache::remember('home:latest-posts', 900, fn () => \App\Models\Post::where('is_published', true)->latest()->take(6)->get()); @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($latestPosts as $post)
                <a href="{{ url('/blog/' . $post->slug) }}" class="cinema-card group bg-gray-900 rounded-2xl border border-gray-800/50 overflow-hidden hover:border-amber-600/40 transition">
                    <div class="aspect-[16/9] bg-gray-800 overflow-hidden">
                        @if($post->image_url)
                            @php $postImg = str_replace(['/w1280/', '/w780/'], '/w500/', $post->image_url); @endphp
                            <img src="{{ $postImg }}" alt="{{ $post->title }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-3xl text-gray-400">📝</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-rose-600/10 text-rose-400 text-[10px] rounded-full">{{ $post->category }}</span>
                            <span class="text-gray-400 text-[10px]">{{ $post->read_time }} dk</span>
                        </div>
                        <h3 class="text-white font-medium text-sm line-clamp-2 group-hover:text-rose-400 transition">{{ $post->title }}</h3>
                        <p class="text-gray-400 text-xs mt-1 line-clamp-2">{{ $post->excerpt }}</p>
                        <p class="text-gray-400 text-[10px] mt-2">{{ $post->published_at->format('d.m.Y') }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
