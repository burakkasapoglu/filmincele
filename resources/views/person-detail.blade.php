@extends('layouts.app')
@section('title', 'Kişi — Filmincele')

@section('content')

@php
$tmdb = app(\App\Services\TmdbService::class);
$data = $tmdb->getPersonDetails($tmdbId);

if ($data):
    $person = [
        'name' => $data['name'] ?? '',
        'biography' => $data['biography'] ?? '',
        'birthday' => $data['birthday'] ?? null,
        'deathday' => $data['deathday'] ?? null,
        'place_of_birth' => $data['place_of_birth'] ?? '',
        'known_for_department' => $data['known_for_department'] ?? '',
        'popularity' => $data['popularity'] ?? 0,
        'profile_path' => $data['profile_path'] ?? null,
    ];

    $extIds = $data['external_ids'] ?? null;
    $imdbUrl = ($extIds['imdb_id'] ?? null) ? 'https://www.imdb.com/name/' . $extIds['imdb_id'] : null;
    $instagramUrl = ($extIds['instagram_id'] ?? null) ? 'https://www.instagram.com/' . $extIds['instagram_id'] : null;
    $twitterUrl = ($extIds['twitter_id'] ?? null) ? 'https://x.com/' . $extIds['twitter_id'] : null;

    $alsoKnownAs = array_values(array_filter($data['also_known_as'] ?? [], fn($n) =>
        preg_match("/^[a-zA-ZığüşöçİĞÜŞÖÇ .\\-']+$/u", $n) && strlen($n) > 1
    ));

    $allItems = [];
    foreach (['movie_credits', 'tv_credits'] as $type) {
        foreach ($data[$type]['cast'] ?? [] as $item) {
            $item['media_type'] = $type === 'tv_credits' ? 'tv' : 'movie';
            $item['sort_date'] = $item['release_date'] ?? $item['first_air_date'] ?? '0000';
            $allItems[] = $item;
        }
    }

    $seen = []; $allCredits = [];
    foreach ($allItems as $item) {
        $key = $item['media_type'] . '-' . $item['id'];
        if (!in_array($key, $seen)) { $seen[] = $key; $allCredits[] = $item; }
    }
    usort($allCredits, fn($a, $b) => strcmp($b['sort_date'], $a['sort_date']));
    $totalCount = count($allCredits);
    $allCredits = array_slice($allCredits, 0, 60);

    $movies = array_values(array_filter($allCredits, fn($m) => ($m['media_type'] ?? 'movie') === 'movie'));
    $tvShows = array_values(array_filter($allCredits, fn($m) => ($m['media_type'] ?? 'movie') === 'tv'));

    $age = $person['birthday'] ? \Carbon\Carbon::parse($person['birthday'])->age : null;
    $yearsActive = null;
    if (!empty($allCredits)) {
        $years = array_filter(array_map(fn($m) => $m['sort_date'] > '0000' ? (int) substr($m['sort_date'], 0, 4) : null, $allCredits));
        if (!empty($years)) $yearsActive = max($years) - min($years);
    }
endif;
@endphp

@if(!empty($person))
<div class="min-h-screen bg-gray-950">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8 mb-10">
            <div class="flex-shrink-0 w-full md:w-72">
                <div class="rounded-2xl overflow-hidden bg-gray-900 border border-gray-800">
                    @if($person['profile_path'])
                        <img src="https://image.tmdb.org/t/p/w342{{ $person['profile_path'] }}" class="w-full aspect-[2/3] object-cover" alt="{{ $person['name'] }}">
                    @else
                        <div class="w-full aspect-[2/3] flex items-center justify-center text-7xl text-gray-700 bg-gray-800">👤</div>
                    @endif
                    <div class="p-5 space-y-3">
                        @if($person['birthday'])
                            <div><span class="text-gray-500 text-xs uppercase tracking-wide">Doğum</span><p class="text-white text-sm">{{ \Carbon\Carbon::parse($person['birthday'])->translatedFormat('d F Y') }}@if(!$person['deathday'] && $age) <span class="text-gray-400">({{ $age }} yaşında)</span>@endif</p></div>
                        @endif
                        @if($person['deathday'])<div><span class="text-gray-500 text-xs uppercase tracking-wide">Ölüm</span><p class="text-white text-sm">{{ \Carbon\Carbon::parse($person['deathday'])->translatedFormat('d F Y') }}</p></div>@endif
                        @if($person['place_of_birth'])<div><span class="text-gray-500 text-xs uppercase tracking-wide">Doğum Yeri</span><p class="text-white text-sm">{{ $person['place_of_birth'] }}</p></div>@endif
                        <div><span class="text-gray-500 text-xs uppercase tracking-wide">Meslek</span><p class="text-white text-sm">{{ $person['known_for_department'] === 'Acting' ? 'Oyuncu' : ($person['known_for_department'] === 'Directing' ? 'Yönetmen' : ($person['known_for_department'] ?? 'Bilinmiyor')) }}</p></div>
                        @if($yearsActive)<div><span class="text-gray-500 text-xs uppercase tracking-wide">Aktif Yıllar</span><p class="text-white text-sm">{{ $yearsActive }} yıl</p></div>@endif
                        @if($person['popularity'])<div><span class="text-gray-500 text-xs uppercase tracking-wide">Popülerlik</span><div class="flex items-center gap-2"><div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden"><div class="h-full bg-rose-500 rounded-full" style="width: {{ min($person['popularity'], 100) }}%"></div></div><span class="text-white text-xs">{{ number_format($person['popularity'], 1) }}</span></div></div>@endif
                        @if($imdbUrl || $instagramUrl || $twitterUrl)
                            <div class="pt-3 border-t border-gray-800"><span class="text-gray-500 text-xs uppercase tracking-wide block mb-2">Bağlantılar</span><div class="flex gap-2">
                                @if($imdbUrl)<a href="{{ $imdbUrl }}" target="_blank" class="px-3 py-1.5 bg-yellow-600/20 text-yellow-500 text-xs rounded-lg hover:bg-yellow-600/30">IMDb</a>@endif
                                @if($instagramUrl)<a href="{{ $instagramUrl }}" target="_blank" class="px-3 py-1.5 bg-pink-600/20 text-pink-400 text-xs rounded-lg hover:bg-pink-600/30">Instagram</a>@endif
                                @if($twitterUrl)<a href="{{ $twitterUrl }}" target="_blank" class="px-3 py-1.5 bg-blue-600/20 text-blue-400 text-xs rounded-lg hover:bg-blue-600/30">X</a>@endif
                            </div></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">{{ $person['name'] }}</h1>
                @if(!empty($alsoKnownAs))<p class="text-gray-500 text-sm mb-4">AKA: {{ implode(', ', array_slice($alsoKnownAs, 0, 5)) }}</p>@endif
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 bg-rose-600/20 text-rose-400 text-sm rounded-full">{{ $person['known_for_department'] === 'Acting' ? 'Oyuncu' : ($person['known_for_department'] === 'Directing' ? 'Yönetmen' : $person['known_for_department']) }}</span>
                    <span class="px-3 py-1 bg-gray-800 text-gray-300 text-sm rounded-full">🎬 {{ count($movies) }} film · 📺 {{ count($tvShows) }} dizi</span>
                </div>
                @if($person['biography'])<div class="mb-8"><h2 class="text-lg font-semibold text-white mb-3">Biyografi</h2><div x-data="{ expanded: false }"><p class="text-gray-400 leading-relaxed text-sm" :class="expanded ? '' : 'line-clamp-4'">{{ $person['biography'] }}</p>@if(strlen($person['biography']) > 400)<button @click="expanded = !expanded" class="text-rose-400 text-sm mt-1 hover:underline"><span x-show="!expanded">Devamını oku ↓</span><span x-show="expanded">Daralt ↑</span></button>@endif</div></div>@endif
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                    <a href="#filmography" onclick="document.getElementById('filmography').scrollIntoView({behavior:'smooth'});return false" class="bg-gray-900 rounded-xl p-4 text-center block hover:border-rose-600/30 border border-gray-800 transition"><p class="text-2xl font-bold text-white">{{ $totalCount }}</p><p class="text-gray-500 text-xs">Toplam İçerik</p></a>
                    <div class="bg-gray-900 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-yellow-400">{{ !empty($allCredits) ? number_format(array_sum(array_map(fn($m) => $m['vote_average'] ?? 0, $allCredits)) / max(1, count($allCredits)), 1) : '—' }}</p><p class="text-gray-500 text-xs">Ort. Puan</p></div>
                    <div class="bg-gray-900 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-white">{{ $person['popularity'] ? number_format($person['popularity'], 0) : '—' }}</p><p class="text-gray-500 text-xs">Popülerlik</p></div>
                    <div class="bg-gray-900 rounded-xl p-4 text-center"><p class="text-2xl font-bold text-white">{{ $yearsActive ?? '—' }}</p><p class="text-gray-500 text-xs">Aktif Yıl</p></div>
                </div>
            </div>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-white mb-6" id="filmography">🎬 Filmografi <span class="text-gray-500 text-lg font-normal ml-2">{{ $totalCount }} içerik</span></h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach([['title' => '🎬 Filmler', 'items' => $movies, 'type' => 'movie'], ['title' => '📺 Diziler', 'items' => $tvShows, 'type' => 'tv']] as $section)
                    <div class="{{ $section['type'] === 'movie' ? 'lg:border-r lg:border-gray-800 lg:pr-4' : 'lg:pl-4' }}">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ $section['title'] }} <span class="text-gray-500 text-sm font-normal">{{ count($section['items']) }}</span></h3>
                        @if(!empty($section['items']))
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($section['items'] as $movie)
                                <a href="{{ $section['type'] === 'tv' ? dizi_url($movie['id'], $movie['name'] ?? '') : film_url($movie['id'], $movie['title'] ?? '') }}" class="group block">
                                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                                        @if($movie['poster_path'])
                                            <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}" loading="lazy" class="w-full h-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $movie['title'] ?? $movie['name'] ?? 'Poster' }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">{{ $section['type'] === 'tv' ? '📺' : '🎬' }}</div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                                            <div class="flex items-center gap-1 text-yellow-400 text-sm">★ {{ number_format($movie['vote_average'] ?? 0, 1) }}</div>
                                        </div>
                                    </div>
                                    <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">{{ $movie['title'] ?? $movie['name'] ?? '' }}</h3>
                                    <p class="text-gray-500 text-xs">{{ isset($movie['release_date']) ? substr($movie['release_date'], 0, 4) : (isset($movie['first_air_date']) ? substr($movie['first_air_date'], 0, 4) : '—') }}@if(isset($movie['character']) && $movie['character']) · {{ $movie['character'] }}@endif@if(isset($movie['job']) && $movie['job']) · {{ $movie['job'] }}@endif</p>
                                </a>
                            @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 text-sm py-8 text-center">Henüz içerik yok</p>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(empty($allCredits))<div class="text-center py-16 text-gray-500"><p class="text-4xl mb-4">🎬</p><p>Henüz içerik bulunamadı.</p></div>@endif
        </div>
    </div>
</div>
@else
<div class="flex items-center justify-center min-h-[60vh]"><div class="text-center"><span class="text-6xl block mb-4">👤</span><h1 class="text-2xl text-white font-bold mb-2">Kişi bulunamadı</h1><a href="{{ url('/') }}" class="inline-block mt-4 px-6 py-2 bg-rose-600 text-white rounded-full">Ana Sayfaya Dön</a></div></div>
@endif
@endsection
