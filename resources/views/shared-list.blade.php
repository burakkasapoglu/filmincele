@extends('layouts.app')
@section('title', $list->name . ' — ' . $list->user->name . ' listesi — Filmincele')
@section('content')

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="text-center mb-10">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-rose-600 to-purple-600 flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
            📋
        </div>
        <h1 class="text-3xl font-bold text-white">{{ $list->name }}</h1>
        @if($list->description)
            <p class="text-gray-400 mt-2">{{ $list->description }}</p>
        @endif
        <div class="flex items-center justify-center gap-3 mt-3">
            <div class="w-8 h-8 rounded-full bg-rose-600/20 flex items-center justify-center text-rose-400 text-xs font-semibold">
                {{ strtoupper(substr($list->user->name, 0, 1)) }}
            </div>
            <span class="text-gray-500 text-sm">{{ $list->user->name }} tarafından</span>
            <span class="text-gray-600">·</span>
            <span class="text-gray-500 text-sm">{{ $list->movies->count() }} film</span>
        </div>
    </div>

    @if($list->movies->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @foreach($list->movies as $movie)
                <a href="{{ film_url($movie->tmdb_id, $movie->title) }}" class="group block">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        @if($movie->poster_path)
                            <img src="https://image.tmdb.org/t/p/w342{{ $movie->poster_path }}" loading="lazy"
                                 class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">🎬</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                            <div class="flex items-center gap-1 text-yellow-400 text-sm">★ {{ number_format($movie->vote_average ?? 0, 1) }}</div>
                        </div>
                    </div>
                    <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">{{ $movie->title }}</h3>
                    <p class="text-gray-500 text-xs">{{ $movie->release_date ? substr($movie->release_date, 0, 4) : '—' }}</p>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-20"><p class="text-gray-500">Bu listede henüz film yok.</p></div>
    @endif

    <div class="text-center mt-12 pt-8 border-t border-gray-800">
        <p class="text-gray-500 text-sm mb-3">Bu listeyi paylaş:</p>
        <div class="flex items-center justify-center gap-3">
            <button onclick="copyToClipboard('{{ url()->current() }}')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-xl transition flex items-center gap-2">
                📋 Linki Kopyala
            </button>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($list->name . ' — ' . $list->user->name . ' listesi') }}&url={{ urlencode(url()->current()) }}" target="_blank"
               class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-xl transition">𝕏 Paylaş</a>
            <a href="https://wa.me/?text={{ urlencode($list->name . ' — ' . $list->user->name . ' listesi ') . urlencode(url()->current()) }}" target="_blank"
               class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm rounded-xl transition">💬 WhatsApp</a>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link kopyalandı! 📋');
    });
}
</script>
@endsection
