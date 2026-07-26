<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-8">
            <span class="text-5xl block mb-2">{{ $moodData['emoji'] }}</span>
            <h1 class="text-3xl font-bold text-white">{{ $moodData['label'] }}</h1>
            <p class="text-gray-400 mt-1 text-sm">{{ $moodData['description'] }} {{ $mediaType === 'tv' ? 'diziler' : 'filmler' }}</p>
        </div>

        <div class="flex gap-2 mb-6 flex-wrap justify-center">
            @foreach(config('moods') as $slug => $m)
                <a href="{{ url('/mod/' . $slug . '?' . http_build_query(['mediaType' => $mediaType])) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition {{ $mood === $slug ? 'bg-rose-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                    {{ $m['emoji'] }} {{ $m['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex gap-6">
            {{-- Sidebar Filters --}}
            <div class="hidden lg:block w-56 flex-shrink-0 space-y-5">
                <div>
                    <div class="flex bg-gray-800 rounded-xl p-1">
                        <a href="{{ url('/mod/' . $mood . '?mediaType=movie') }}"
                           class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $mediaType === 'movie' ? 'bg-rose-600 text-white' : 'text-gray-400 hover:text-white' }}">
                            🎬 Film
                        </a>
                        <a href="{{ url('/mod/' . $mood . '?mediaType=tv') }}"
                           class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $mediaType === 'tv' ? 'bg-rose-600 text-white' : 'text-gray-400 hover:text-white' }}">
                            📺 Dizi
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-gray-400 text-xs uppercase tracking-wide mb-2 font-semibold">Sırala</h4>
                    <div class="space-y-1">
                        @php $sorts = [
                            'popularity.desc' => '🔥 En Popüler',
                            'vote_average.desc' => '⭐ En Yüksek Puan',
                            'vote_count.desc' => '📊 En Çok Oylanan',
                            ($mediaType === 'tv' ? 'first_air_date' : 'release_date') . '.desc' => '🆕 En Yeni',
                        ]; @endphp
                        @foreach($sorts as $val => $label)
                            <button wire:click="$set('sortBy', '{{ $val }}')"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm transition {{ $sortBy === $val ? 'bg-rose-600/20 text-rose-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-4">
                    <h4 class="text-gray-400 text-xs uppercase tracking-wide mb-2 font-semibold">IMDb Puanı</h4>
                    <div class="grid grid-cols-3 gap-1">
                        @foreach([6,7,8] as $r)
                            <button wire:click="$set('ratingMin', {{ $ratingMin === $r ? 'null' : $r }})"
                                    class="px-2 py-1.5 rounded-lg text-xs text-center transition {{ $ratingMin === $r ? 'bg-rose-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' }}">
                                {{ $r }}+
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-4">
                    <h4 class="text-gray-400 text-xs uppercase tracking-wide mb-2 font-semibold">Bölge</h4>
                    <div class="space-y-1">
                        @php $regions = [
                            null => '🌍 Tüm Dünya',
                            'western' => '🌎 Batı Sineması',
                            'US' => '🇺🇸 Hollywood',
                            'GB' => '🇬🇧 İngiltere',
                            'KR' => '🇰🇷 Güney Kore',
                            'TR' => '🇹🇷 Türkiye',
                            'FR' => '🇫🇷 Fransa',
                            'DE' => '🇩🇪 Almanya',
                            'JP' => '🇯🇵 Japonya',
                            'IN' => '🇮🇳 Hindistan',
                        ]; @endphp
                        @foreach($regions as $val => $label)
                            <button wire:click="$set('region', {{ $val === null ? 'null' : "'" . $val . "'" }})"
                                    class="w-full text-left px-3 py-1.5 rounded-lg text-xs transition {{ $region === $val ? 'bg-rose-600/20 text-rose-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-4">
                    <h4 class="text-gray-400 text-xs uppercase tracking-wide mb-2 font-semibold">Platformun</h4>
                    <div class="space-y-1">
                        @php $platforms = [
                            null => '📺 Tüm Platformlar',
                            '8' => 'Netflix',
                            '337' => 'Disney+',
                            '119' => 'Amazon Prime',
                            '384' => 'HBO Max',
                            '350' => 'Apple TV+',
                            '531' => 'Paramount+',
                            '1899' => 'Max',
                        ]; @endphp
                        @foreach($platforms as $val => $label)
                            <button wire:click="$set('watchProvider', {{ $val === null ? 'null' : "'" . $val . "'" }})"
                                    class="w-full text-left px-3 py-1.5 rounded-lg text-xs transition {{ $watchProvider === $val ? 'bg-rose-600/20 text-rose-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-4">
                    <h4 class="text-gray-400 text-xs uppercase tracking-wide mb-2 font-semibold">Yıl</h4>
                    <div class="flex gap-2">
                        <select wire:model.live="yearFrom" class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-2 py-1.5 text-white text-xs">
                            <option value="">Başlangıç</option>
                            @foreach(range($currentYear, 1950) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                        <select wire:model.live="yearTo" class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-2 py-1.5 text-white text-xs">
                            <option value="">Bitiş</option>
                            @foreach(range($currentYear, 1950) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($sortBy !== 'popularity.desc' || $ratingMin || $yearFrom || $yearTo || $region || $watchProvider)
                    <button wire:click="resetFilters" class="w-full py-2 text-xs text-gray-500 hover:text-white transition">
                        Filtreleri Sıfırla
                    </button>
                @endif
            </div>

            {{-- Movie Grid --}}
            <div class="flex-1 min-w-0">
                {{-- Mobile controls --}}
                <div class="lg:hidden flex gap-2 mb-4 flex-wrap">
                    <div class="flex bg-gray-800 rounded-xl p-1 flex-1">
                        <button wire:click="$set('mediaType', 'movie')"
                                class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $mediaType === 'movie' ? 'bg-rose-600 text-white' : 'text-gray-400' }}">
                            Film
                        </button>
                        <button wire:click="$set('mediaType', 'tv')"
                                class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $mediaType === 'tv' ? 'bg-rose-600 text-white' : 'text-gray-400' }}">
                            Dizi
                        </button>
                    </div>
                    <select wire:model.live="sortBy" class="bg-gray-800 text-white border border-gray-700 rounded-lg px-3 py-2 text-xs flex-1">
                        <option value="popularity.desc">En Popüler</option>
                        <option value="vote_average.desc">En Yüksek Puan</option>
                        <option value="vote_count.desc">En Çok Oylanan</option>
                        <option value="{{ $mediaType === 'tv' ? 'first_air_date' : 'release_date' }}.desc">En Yeni</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($movies as $movie)
                        @php
                            $mTitle = $movie['title'] ?? $movie['name'] ?? '';
                            $mDate = $movie['release_date'] ?? $movie['first_air_date'] ?? null;
                            $mUrl = $mediaType === 'tv' ? dizi_url($movie['id'], $mTitle) : film_url($movie['id'], $mTitle);
                        @endphp
                        <a href="{{ $mUrl }}" class="group block">
                            <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800 mb-2">
                                @if($movie['poster_path'])
                                    <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                         alt="{{ $mTitle }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-3xl text-gray-600">{{ $mediaType === 'tv' ? '📺' : '🎬' }}</div>
                                @endif
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-0.5 bg-black/60 backdrop-blur-sm rounded-lg text-xs font-semibold text-white">
                                        ★ {{ number_format($movie['vote_average'] ?? 0, 1) }}
                                    </span>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                                    <p class="text-white text-xs line-clamp-3">{{ $movie['overview'] ?? '' }}</p>
                                </div>
                            </div>
                            <h3 class="text-white text-sm font-medium leading-tight line-clamp-2 group-hover:text-rose-400 transition">
                                {{ $mTitle }}
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                <span>{{ $mDate ? substr($mDate, 0, 4) : '—' }}</span>
                                <span>· {{ number_format($movie['vote_count'] ?? 0) }} oy</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if(empty($movies))
                    <div class="text-center py-20">
                        <span class="text-5xl block mb-4">🍿</span>
                        <p class="text-gray-400">Bu filtrelere uygun içerik bulunamadı.</p>
                        <button wire:click="resetFilters" class="mt-3 px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-sm rounded-xl transition">Filtreleri Sıfırla</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
