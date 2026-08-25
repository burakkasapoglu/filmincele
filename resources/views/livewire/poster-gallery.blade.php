<div class="min-h-screen bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">🎞️ Film Afişleri</h1>
            <p class="text-gray-400 text-sm">Popüler filmlerin afiş arşivi — büyütmek için afişe tıkla</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($items as $movie)
                @php
                    $slug = \Illuminate\Support\Str::slug($movie['title'] ?? '');
                    $detailUrl = url('/film/' . $movie['id'] . '-' . $slug);
                @endphp
                <div class="cinema-card group block cursor-pointer"
                     onclick="openPoster(this)"
                     data-title="{{ $movie['title'] }}"
                     data-poster="https://image.tmdb.org/t/p/w780{{ $movie['poster_path'] }}"
                     data-year="{{ substr($movie['release_date'] ?? '—', 0, 4) }}"
                     data-rating="{{ $movie['vote_average'] ?? 0 }}"
                     data-overview="{{ $movie['overview'] ?? '' }}"
                     data-url="{{ $detailUrl }}">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                        <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                             srcset="https://image.tmdb.org/t/p/w185{{ $movie['poster_path'] }} 1x, https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }} 2x"
                             alt="{{ $movie['title'] }} afişi"
                             class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
                             loading="lazy">
                        @if($movie['vote_average'] ?? 0)
                            <span class="absolute top-2 left-2 px-2 py-0.5 bg-black/70 backdrop-blur-sm rounded-lg text-xs font-semibold text-amber-400">★ {{ number_format($movie['vote_average'], 1) }}</span>
                        @endif
                        <span class="absolute bottom-2 right-2 w-8 h-8 bg-black/70 backdrop-blur-sm rounded-full flex items-center justify-center text-white text-sm opacity-0 group-hover:opacity-100 transition">🔍</span>
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                            <p class="text-white text-xs line-clamp-3">{{ $movie['overview'] ?? '' }}</p>
                        </div>
                    </div>
                    <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-amber-400 transition">{{ $movie['title'] }}</h3>
                    <p class="text-gray-500 text-xs">{{ substr($movie['release_date'] ?? '—', 0, 4) }}</p>
                </div>
            @endforeach
        </div>

        {{-- Lightbox --}}
        <div id="poster-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" onclick="closePoster()"></div>
            <div id="poster-lightbox-card" class="relative z-10 max-w-3xl w-full bg-gray-900 rounded-2xl overflow-hidden shadow-2xl border border-gray-800" style="animation: posterZoom .3s cubic-bezier(0.16,1,0.3,1)">
                <button onclick="closePoster()"
                        class="absolute top-3 right-3 z-20 w-9 h-9 bg-black/70 backdrop-blur-sm border border-gray-700 rounded-full text-gray-300 hover:text-white transition flex items-center justify-center">✕</button>
                <img id="lb-poster" src="" alt="" class="w-full max-h-[70vh] object-contain bg-gray-950">
                <div class="p-5 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h3 id="lb-title" class="text-white font-bold text-lg truncate"></h3>
                        <div class="flex items-center gap-3 text-sm text-gray-400 mt-1">
                            <span id="lb-year"></span>
                            <span id="lb-rating" class="text-amber-400 font-semibold"></span>
                        </div>
                        <p id="lb-overview" class="text-gray-400 text-sm mt-2 line-clamp-2 max-w-md"></p>
                    </div>
                    <a id="lb-url" href="#"
                       class="flex-shrink-0 px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold rounded-xl transition whitespace-nowrap">
                        Filme Git →
                    </a>
                </div>
            </div>
        </div>

        <style>
            @keyframes posterZoom {
                from { opacity: 0; transform: scale(0.92) translateY(16px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }
        </style>

        <script>
            function openPoster(el) {
                const lb = document.getElementById('poster-lightbox');
                document.getElementById('lb-poster').src = el.dataset.poster;
                document.getElementById('lb-poster').alt = el.dataset.title;
                document.getElementById('lb-title').textContent = el.dataset.title;
                document.getElementById('lb-year').textContent = el.dataset.year;
                document.getElementById('lb-rating').textContent = parseFloat(el.dataset.rating) > 0 ? '★ ' + parseFloat(el.dataset.rating).toFixed(1) : '';
                document.getElementById('lb-overview').textContent = el.dataset.overview;
                document.getElementById('lb-url').href = el.dataset.url;

                lb.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closePoster() {
                document.getElementById('poster-lightbox').style.display = 'none';
                document.body.style.overflow = '';
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closePoster();
            });
        </script>

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
