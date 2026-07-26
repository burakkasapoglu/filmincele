@extends('layouts.app')
@section('title', 'Ülke İçerikleri — Filmincele')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    @php
        $tmdb = app(\App\Services\TmdbService::class);
        $countryCode = request()->route('countryCode');
        $movies = $tmdb->discoverByCountry($countryCode);

        $countryNames = [
            'TR' => 'Türkiye', 'US' => 'ABD', 'GB' => 'İngiltere', 'FR' => 'Fransa',
            'DE' => 'Almanya', 'IT' => 'İtalya', 'ES' => 'İspanya', 'JP' => 'Japonya',
            'KR' => 'Güney Kore', 'IN' => 'Hindistan', 'CN' => 'Çin', 'CA' => 'Kanada',
            'AU' => 'Avustralya', 'BR' => 'Brezilya', 'MX' => 'Meksika', 'RU' => 'Rusya',
            'SE' => 'İsveç', 'DK' => 'Danimarka', 'NL' => 'Hollanda', 'BE' => 'Belçika',
        ];
        $countryName = request()->route('name') ?? ($countryNames[$countryCode] ?? $countryCode);
    @endphp

    <div class="flex items-center gap-4 mb-8">
        <div class="text-4xl">🎬</div>
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $countryName }} İçerikleri</h1>
            <p class="text-gray-400">Bu ülkeden en popüler filmler</p>
        </div>
    </div>

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
                </div>
                <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">
                    {{ $movie['title'] }}
                </h3>
                <p class="text-gray-500 text-xs">{{ substr($movie['release_date'] ?? '—', 0, 4) }} · ★ {{ number_format($movie['vote_average'] ?? 0, 1) }}</p>
            </a>
        @endforeach
    </div>

    @if(empty($movies))
        <div class="text-center py-20 text-gray-500">
            <p class="text-4xl mb-4">🎬</p>
            <p>Bu ülkeye ait içerik bulunamadı.</p>
        </div>
    @endif
</div>
@endsection
