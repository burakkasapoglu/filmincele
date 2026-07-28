@extends('layouts.app')
@section('title', 'Profilim — Filmincele')

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => alert('Link kopyalandı! 📋'));
}
</script>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    @php
        $user = Auth::user();
        $ratingCount = $user->ratings()->count();
        $reviewCount = $user->ratings()->whereNotNull('review')->where('review', '!=', '')->count();
        $listCount = $user->watchlists()->count();
        $avgRating = round($user->ratings()->avg('rating') ?? 0, 1);
        $watchlists = $user->watchlists()->with(['movies' => fn($q) => $q->orderBy('watchlist_movie.added_at', 'desc')->take(6)])->get();

        $strength = ($user->name ? 15 : 0) + ($user->birth_date ? 15 : 0) + ($user->profile_photo_path ? 15 : 0)
            + ($ratingCount > 0 ? 15 : 0) + ($listCount > 0 ? 15 : 0)
            + ($user->bio ? 10 : 0) + ($user->location ? 10 : 0) + ($user->website ? 5 : 0);

        $badges = [];
        if ($ratingCount >= 1) $badges[] = 'first_rating';
        if ($ratingCount >= 10) $badges[] = 'rating_10';
        if ($ratingCount >= 50) $badges[] = 'rating_50';
        if ($ratingCount >= 100) $badges[] = 'rating_100';
        if ($reviewCount >= 5) $badges[] = 'review_5';
        if ($listCount >= 3) $badges[] = 'list_3';
        if ($strength >= 85) $badges[] = 'profile_complete';
        if ($user->profile_photo_path) $badges[] = 'photo_added';
        if ($user->birth_date) $badges[] = 'birthday_set';
        if ($user->bio) $badges[] = 'bio_writer';
        if ($user->location) $badges[] = 'location_set';
    @endphp

    {{-- Header Card --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-5">
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-800 flex-shrink-0">
                <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                        @if($user->location)
                            <p class="text-gray-500 text-xs mt-0.5">📍 {{ $user->location }}</p>
                        @endif
                        @if($user->website)
                            <a href="{{ $user->website }}" target="_blank" class="text-rose-400 text-xs hover:underline mt-0.5 block">🔗 {{ parse_url($user->website, PHP_URL_HOST) }}</a>
                        @endif
                    </div>
                    <a href="{{ url('/profil/duzenle') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-xl transition">✏️ Düzenle</a>
                </div>
                @if($user->bio)
                    <p class="text-gray-400 text-sm mt-3 leading-relaxed">{{ $user->bio }}</p>
                @endif
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs text-gray-500">Profil gücü</span>
                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden max-w-xs">
                        <div class="h-full rounded-full transition-all {{ $strength >= 80 ? 'bg-emerald-500' : ($strength >= 40 ? 'bg-yellow-500' : 'bg-rose-500') }}" style="width: {{ $strength }}%"></div>
                    </div>
                    <span class="text-xs font-medium {{ $strength >= 80 ? 'text-emerald-400' : ($strength >= 40 ? 'text-yellow-400' : 'text-rose-400') }}">{{ $strength }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Clickable Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6" id="stats">
        <a href="#ratings" onclick="document.getElementById('ratings').scrollIntoView({behavior:'smooth'})" class="bg-gray-900 rounded-2xl border border-gray-800/50 p-4 text-center hover:border-rose-600/30 transition group">
            <p class="text-2xl font-bold text-yellow-400 group-hover:scale-110 transition">{{ $ratingCount }}</p>
            <p class="text-gray-500 text-xs group-hover:text-rose-400 transition">Puan</p>
        </a>
        <a href="#lists" onclick="document.getElementById('lists').scrollIntoView({behavior:'smooth'})" class="bg-gray-900 rounded-2xl border border-gray-800/50 p-4 text-center hover:border-rose-600/30 transition group">
            <p class="text-2xl font-bold text-white group-hover:scale-110 transition">{{ $listCount }}</p>
            <p class="text-gray-500 text-xs group-hover:text-rose-400 transition">Liste</p>
        </a>
        <a href="#badges-section" onclick="document.getElementById('badges-section').scrollIntoView({behavior:'smooth'})" class="bg-gray-900 rounded-2xl border border-gray-800/50 p-4 text-center hover:border-rose-600/30 transition group">
            <p class="text-2xl font-bold text-white group-hover:scale-110 transition">{{ count($badges) }}</p>
            <p class="text-gray-500 text-xs group-hover:text-rose-400 transition">Rozet</p>
        </a>
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-4 text-center">
            <p class="text-2xl font-bold text-emerald-400">{{ $avgRating }}</p>
            <p class="text-gray-500 text-xs">Ort. Puan</p>
        </div>
    </div>

    {{-- Badges --}}
    <div class="mb-6" id="badges-section">
        <h2 class="text-white font-semibold mb-3">🏅 Rozetler</h2>
        <div class="flex flex-wrap gap-3">
            @foreach(config('badges') as $badge)
                @php $earned = in_array($badge['id'], $badges); @endphp
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm {{ $earned ? 'bg-gray-900 border border-gray-700' : 'bg-gray-900/30 opacity-30' }}">
                    <span class="text-xl {{ $earned ? '' : 'grayscale' }}">{{ $badge['emoji'] }}</span>
                    <div>
                        <p class="text-white text-xs font-medium">{{ $badge['name'] }}</p>
                        <p class="text-gray-500 text-[10px]">{{ $badge['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- My Lists --}}
    <div class="mb-6" id="lists">
        <h2 class="text-white font-semibold mb-3">📋 Listelerim <span class="text-gray-500 text-xs font-normal">({{ $listCount }})</span></h2>
        @if($watchlists->count())
            <div class="space-y-4">
                @foreach($watchlists as $list)
                    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-white font-medium">{{ $list->name }}</h3>
                                <p class="text-gray-500 text-xs">{{ $list->movies->count() }} film</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($list->share_token)
                                    <button onclick="copyToClipboard('{{ url('/liste/' . $list->share_token) }}')"
                                            class="text-xs text-gray-500 hover:text-rose-400 transition" title="Linki kopyala">🔗</button>
                                @endif
                            </div>
                        </div>
                        @if($list->movies->count())
                            <div class="flex gap-3 overflow-x-auto pb-2">
                                @foreach($list->movies as $movie)
                                    <a href="{{ url('/film/' . $movie->tmdb_id . '-' . \Illuminate\Support\Str::slug($movie->title ?? '')) }}" class="flex-shrink-0 w-20 group">
                                        <div class="aspect-[2/3] rounded-lg overflow-hidden bg-gray-800 mb-1">
                                            @if($movie->poster_path)
                                                <img src="https://image.tmdb.org/t/p/w154{{ $movie->poster_path }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xl text-gray-600">🎬</div>
                                            @endif
                                        </div>
                                        <p class="text-white text-[10px] leading-tight line-clamp-2 group-hover:text-rose-400 transition">{{ $movie->title }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 text-xs py-3 text-center">Henüz film eklenmemiş.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-8 text-center">
                <p class="text-4xl mb-3">📋</p>
                <p class="text-gray-400 text-sm">Henüz liste oluşturmadın.</p>
                <p class="text-gray-600 text-xs mt-1">Film sayfalarındaki "Listeme Ekle" butonu ile film eklemeye başla!</p>
            </div>
        @endif
    </div>

    {{-- Recent Ratings --}}
    <div id="ratings">
        <h2 class="text-white font-semibold mb-3">⭐ Son Puanlamalarım <span class="text-gray-500 text-xs font-normal">({{ $ratingCount }})</span></h2>
        @php $recentRatings = $user->ratings()->with('movie')->latest()->take(8)->get(); @endphp
        @if($recentRatings->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($recentRatings as $r)
                    <a href="{{ url('/film/' . ($r->movie->tmdb_id ?? '#') . '-' . \Illuminate\Support\Str::slug($r->movie->title ?? '')) }}"
                       class="flex items-center gap-3 bg-gray-900 rounded-xl border border-gray-800/50 p-3 hover:bg-gray-800 transition group">
                        <div class="w-10 h-10 rounded-lg bg-yellow-600/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-yellow-400 text-sm font-bold">{{ $r->rating }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-white text-sm group-hover:text-rose-400 transition truncate">{{ $r->movie->title ?? 'Film' }}</p>
                            @if($r->review)
                                <p class="text-gray-500 text-xs truncate mt-0.5">{{ $r->review }}</p>
                            @endif
                        </div>
                        <span class="text-gray-600 text-xs flex-shrink-0">{{ $r->created_at->format('d.m') }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-8 text-center">
                <p class="text-4xl mb-3">⭐</p>
                <p class="text-gray-400 text-sm">Henüz film puanlamadın.</p>
            </div>
        @endif
    </div>
</div>
@endsection
