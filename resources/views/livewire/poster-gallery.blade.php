<div class="min-h-screen bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">🎞️ Film Afişleri</h1>
            <p class="text-gray-400 text-sm">Popüler filmlerin afiş arşivi — koleksiyonuna eklemek için afişe tıkla</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($items as $movie)
                @php
                    $slug = \Illuminate\Support\Str::slug($movie['title'] ?? '');
                    $moodSlug = config('genre-mood-map')[$movie['genre_ids'][0] ?? 0] ?? null;
                @endphp
                <a href="{{ url('/film/' . $movie['id'] . '-' . $slug) }}" class="cinema-card group block">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                             srcset="https://image.tmdb.org/t/p/w185{{ $movie['poster_path'] }} 1x, https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }} 2x"
                             alt="{{ $movie['title'] }} afişi"
                             class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
                             loading="lazy">
                        @if($movie['vote_average'] ?? 0)
                            <span class="absolute top-2 left-2 px-2 py-0.5 bg-black/70 backdrop-blur-sm rounded-lg text-xs font-semibold text-amber-400">★ {{ number_format($movie['vote_average'], 1) }}</span>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                            <p class="text-white text-xs line-clamp-3">{{ $movie['overview'] ?? '' }}</p>
                        </div>
                    </div>
                    <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-amber-400 transition">{{ $movie['title'] }}</h3>
                    <p class="text-gray-500 text-xs">{{ substr($movie['release_date'] ?? '—', 0, 4) }}</p>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-center gap-2 mt-12 flex-wrap">
            @if($page > 1)
                <button wire:click="goToPage({{ $page - 1 }})" class="px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition">← Önceki</button>
            @endif

            @php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
            @endphp

            @if($start > 1)
                <button wire:click="goToPage(1)" class="px-3 py-2 bg-gray-900 border border-gray-800 rounded-xl text-sm text-gray-400 hover:text-white transition">1</button>
                @if($start > 2)<span class="text-gray-600 px-1">…</span>@endif
            @endif

            @for($p = $start; $p <= $end; $p++)
                <button wire:click="goToPage({{ $p }})"
                        class="px-3 py-2 rounded-xl text-sm transition {{ $p === $page ? 'bg-amber-600 text-white font-semibold' : 'bg-gray-900 border border-gray-800 text-gray-400 hover:text-white' }}">
                    {{ $p }}
                </button>
            @endfor

            @if($end < $totalPages)
                @if($end < $totalPages - 1)<span class="text-gray-600 px-1">…</span>@endif
                <button wire:click="goToPage({{ $totalPages }})" class="px-3 py-2 bg-gray-900 border border-gray-800 rounded-xl text-sm text-gray-400 hover:text-white transition">{{ $totalPages }}</button>
            @endif

            @if($page < $totalPages)
                <button wire:click="goToPage({{ $page + 1 }})" class="px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition">Sonraki →</button>
            @endif
        </div>

        <p class="text-center text-gray-600 text-xs mt-4">Sayfa {{ $page }} / {{ $totalPages }} — {{ count($items) }} afiş gösteriliyor</p>
    </div>
</div>
