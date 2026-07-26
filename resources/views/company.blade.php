@extends('layouts.app')
@section('title', ($company['name'] ?? 'Şirket') . ' İçerikleri — Filmincele')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    @php
        $tmdb = app(\App\Services\TmdbService::class);
        $companyId = (int) request()->route('companyId');
        $company = $tmdb->getCompanyDetails($companyId);
        $movies = $tmdb->getCompanyMovies($companyId);
    @endphp

    @if($company)
        <div class="flex items-center gap-4 mb-8">
            <div class="w-16 h-16 rounded-xl bg-gray-800 flex items-center justify-center overflow-hidden">
                @if($company['logo_path'])
                    <img src="https://image.tmdb.org/t/p/w92{{ $company['logo_path'] }}"
                         alt="{{ $company['name'] }}"
                         class="w-full h-full object-contain p-2">
                @else
                    <span class="text-3xl">🏢</span>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $company['name'] }}</h1>
                @if($company['headquarters'] ?? null)
                    <p class="text-gray-400 text-sm">{{ $company['headquarters'] }}</p>
                @endif
                @if($company['origin_country'] ?? null)
                    <p class="text-gray-500 text-xs">🇺🇸 {{ $company['origin_country'] }}</p>
                @endif
            </div>
        </div>
    @else
        <h1 class="text-2xl font-bold text-white mb-8">{{ request()->route('name', 'Yapım Şirketi') }}</h1>
    @endif

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
            <p class="text-4xl mb-4">🏢</p>
            <p>Bu şirkete ait içerik bulunamadı.</p>
        </div>
    @endif
</div>
@endsection
