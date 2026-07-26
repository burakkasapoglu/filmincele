<div class="relative w-full max-w-md" x-data="{ open: @entangle('showDropdown') }">
    <div class="relative">
        <input type="text"
               wire:model.live.debounce.300ms="query"
               wire:keydown.enter="search"
               placeholder="Film, dizi, oyuncu ara..."
               class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-10 pr-4 py-2.5 text-white text-sm placeholder-gray-500 focus:ring-rose-500 focus:border-rose-500 transition"
               @focus="if($wire.query.length >= 2) $wire.showDropdown = true"
               @keydown.escape="$wire.showDropdown = false">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            🔍
        </div>
        @if($query)
            <button wire:click="$set('query', '')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white text-xs">
                ✕
            </button>
        @endif
    </div>

    @if($showDropdown && !empty($suggestions))
        <div class="absolute top-full left-0 right-0 mt-2 bg-gray-900 border border-gray-700 rounded-xl shadow-xl z-50 overflow-hidden"
             wire:click.away="$set('showDropdown', false)">
            @foreach($suggestions as $item)
                <button wire:click="selectSuggestion({{ $item['id'] }}, '{{ $item['media_type'] }}')"
                        class="w-full text-left px-4 py-3 flex items-center gap-3 hover:bg-gray-800 transition border-b border-gray-800 last:border-0">
                    {{-- Thumbnail --}}
                    <div class="w-10 h-12 rounded-lg overflow-hidden bg-gray-800 flex-shrink-0">
                        @if($item['poster_path'] ?? $item['profile_path'] ?? null)
                            <img src="https://image.tmdb.org/t/p/w92{{ $item['poster_path'] ?? $item['profile_path'] }}"
                                 class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-lg">
                                {{ ($item['media_type'] ?? '') === 'person' ? '👤' : '🎬' }}
                            </div>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-white text-sm font-medium truncate">
                            {{ $item['title'] ?? $item['name'] ?? '' }}
                        </p>
                        <p class="text-gray-500 text-xs">
                            @if(($item['media_type'] ?? '') === 'person')
                                {{ $item['known_for_department'] ?? 'Kişi' }}
                                @if($item['known_for_department'] ?? null)
                                    {{ $item['known_for_department'] === 'Acting' ? 'Oyuncu' : ($item['known_for_department'] === 'Directing' ? 'Yönetmen' : $item['known_for_department']) }}
                                @endif
                            @else
                                {{ ($item['media_type'] ?? '') === 'tv' ? 'Dizi' : 'Film' }}
                                @if($item['release_date'] ?? $item['first_air_date'] ?? null)
                                    · {{ substr($item['release_date'], 0, 4) }}
                                @endif
                                @if($item['vote_average'] ?? 0 > 0)
                                    · ★ {{ number_format($item['vote_average'], 1) }}
                                @endif
                            @endif
                        </p>
                    </div>
                    <span class="text-gray-600 text-xs flex-shrink-0">
                        {{ ($item['media_type'] ?? '') === 'person' ? 'Kişi' : (($item['media_type'] ?? '') === 'tv' ? 'Dizi' : 'Film') }}
                    </span>
                </button>
            @endforeach

            <a href="{{ url('/kesfet?q=' . urlencode($query)) }}"
               class="block text-center px-4 py-3 text-sm text-rose-400 hover:bg-gray-800 transition border-t border-gray-800">
                "{{ $query }}" için tüm sonuçları gör →
            </a>
        </div>
    @endif
</div>
