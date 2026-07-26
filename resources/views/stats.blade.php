@extends('layouts.app')
@section('title', 'İstatistikler — Filmincele')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-8">📊 Site İstatistikleri</h1>

    @php
        $userCount = App\Models\User::count();
        $ratingCount = App\Models\Rating::count();
        $listCount = App\Models\Watchlist::count();
        $postCount = App\Models\Post::where('is_published', true)->count();
        $topRated = App\Models\Rating::selectRaw('movie_id, AVG(rating) as avg, COUNT(*) as count')
            ->with('movie')->groupBy('movie_id')->orderByDesc('avg')->take(8)->get();
        $mostActive = App\Models\User::withCount('ratings')->orderByDesc('ratings_count')->take(5)->get();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-5 text-center"><p class="text-3xl font-bold text-white">{{ $userCount }}</p><p class="text-gray-500 text-xs mt-1">Üye</p></div>
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-5 text-center"><p class="text-3xl font-bold text-yellow-400">{{ $ratingCount }}</p><p class="text-gray-500 text-xs mt-1">Puanlama</p></div>
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-5 text-center"><p class="text-3xl font-bold text-white">{{ $listCount }}</p><p class="text-gray-500 text-xs mt-1">Liste</p></div>
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-5 text-center"><p class="text-3xl font-bold text-rose-400">{{ $postCount }}</p><p class="text-gray-500 text-xs mt-1">Blog</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-5">
            <h2 class="text-white font-semibold mb-4">En Yüksek Puanlı Filmler</h2>
            @foreach($topRated as $r)
                <div class="flex items-center gap-3 py-2 border-b border-gray-800 last:border-0">
                    <span class="text-yellow-400 font-bold text-sm w-10">★ {{ number_format($r->avg, 1) }}</span>
                    <span class="text-white text-sm flex-1 truncate">{{ $r->movie->title ?? 'Film' }}</span>
                    <span class="text-gray-500 text-xs">{{ $r->count }} oy</span>
                </div>
            @endforeach
        </div>
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-5">
            <h2 class="text-white font-semibold mb-4">En Aktif Üyeler</h2>
            @foreach($mostActive as $user)
                <div class="flex items-center gap-3 py-2 border-b border-gray-800 last:border-0">
                    <div class="w-8 h-8 rounded-full bg-rose-600/20 flex items-center justify-center text-rose-400 text-xs font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <span class="text-white text-sm flex-1">{{ $user->name }}</span>
                    <span class="text-yellow-400 text-xs">{{ $user->ratings_count }} puan</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
