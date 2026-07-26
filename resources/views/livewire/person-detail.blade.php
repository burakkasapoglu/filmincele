<div class="min-h-screen bg-gray-950">
    @if($person)
        <div class="max-w-5xl mx-auto px-4 py-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row gap-8 mb-10">
                {{-- Photo + Info Card (Wikipedia style) --}}
                <div class="flex-shrink-0 w-full md:w-72">
                    <div class="rounded-2xl overflow-hidden bg-gray-900 border border-gray-800">
                        @if($person['profile_path'])
                            <img src="https://image.tmdb.org/t/p/w342{{ $person['profile_path'] }}"
                                 alt="{{ $person['name'] }}"
                                 class="w-full aspect-[2/3] object-cover">
                        @else
                            <div class="w-full aspect-[2/3] flex items-center justify-center text-7xl text-gray-700 bg-gray-800">👤</div>
                        @endif

                        <div class="p-5 space-y-3">
                            @if($person['birthday'])
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Doğum</span>
                                    <p class="text-white text-sm">
                                        {{ \Carbon\Carbon::parse($person['birthday'])->format('d F Y') }}
                                        @if(!$person['deathday'] && $age)
                                            <span class="text-gray-400">({{ $age }} yaşında)</span>
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if($person['deathday'])
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Ölüm</span>
                                    <p class="text-white text-sm">
                                        {{ \Carbon\Carbon::parse($person['deathday'])->format('d F Y') }}
                                        @php $deathAge = \Carbon\Carbon::parse($person['birthday'])->diffInYears($person['deathday']); @endphp
                                        <span class="text-gray-400">({{ $deathAge }} yaşında)</span>
                                    </p>
                                </div>
                            @endif

                            @if($person['place_of_birth'])
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Doğum Yeri</span>
                                    <p class="text-white text-sm">{{ $person['place_of_birth'] }}</p>
                                </div>
                            @endif

                            <div>
                                <span class="text-gray-500 text-xs uppercase tracking-wide">Meslek</span>
                                <p class="text-white text-sm">
                                    {{ $person['known_for_department'] === 'Acting' ? 'Oyuncu' : ($person['known_for_department'] === 'Directing' ? 'Yönetmen' : ($person['known_for_department'] ?? 'Bilinmiyor')) }}
                                </p>
                            </div>

                            @if($yearsActive)
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Aktif Yıllar</span>
                                    <p class="text-white text-sm">{{ $yearsActive }} yıl</p>
                                </div>
                            @endif

                            @if($person['popularity'])
                                <div>
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Popülerlik</span>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-rose-500 rounded-full" style="width: {{ min($person['popularity'], 100) }}%"></div>
                                        </div>
                                        <span class="text-white text-xs">{{ number_format($person['popularity'], 1) }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- External Links --}}
                            @if($imdbUrl || $instagramUrl || $twitterUrl)
                                <div class="pt-3 border-t border-gray-800">
                                    <span class="text-gray-500 text-xs uppercase tracking-wide block mb-2">Bağlantılar</span>
                                    <div class="flex gap-2">
                                        @if($imdbUrl)
                                            <a href="{{ $imdbUrl }}" target="_blank" class="px-3 py-1.5 bg-yellow-600/20 text-yellow-500 text-xs rounded-lg hover:bg-yellow-600/30 transition">
                                                IMDb
                                            </a>
                                        @endif
                                        @if($instagramUrl)
                                            <a href="{{ $instagramUrl }}" target="_blank" class="px-3 py-1.5 bg-pink-600/20 text-pink-400 text-xs rounded-lg hover:bg-pink-600/30 transition">
                                                Instagram
                                            </a>
                                        @endif
                                        @if($twitterUrl)
                                            <a href="{{ $twitterUrl }}" target="_blank" class="px-3 py-1.5 bg-blue-600/20 text-blue-400 text-xs rounded-lg hover:bg-blue-600/30 transition">
                                                X/Twitter
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="flex-1">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">{{ $person['name'] }}</h1>

                    @if(!empty($alsoKnownAs))
                        <p class="text-gray-500 text-sm mb-4">
                            AKA: {{ implode(', ', array_slice($alsoKnownAs, 0, 5)) }}
                        </p>
                    @endif

                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="px-3 py-1 bg-rose-600/20 text-rose-400 text-sm rounded-full">
                            {{ $person['known_for_department'] === 'Acting' ? 'Oyuncu' : ($person['known_for_department'] === 'Directing' ? 'Yönetmen' : $person['known_for_department']) }}
                        </span>
                        <span class="px-3 py-1 bg-gray-800 text-gray-300 text-sm rounded-full">
                            🎬 {{ $totalCount > 60 ? '60 / ' . $totalCount : $totalCount }} içerik
                        </span>
                    </div>

                    {{-- Biography --}}
                    @if($person['biography'])
                        <div class="mb-8">
                            <h2 class="text-lg font-semibold text-white mb-3">Biyografi</h2>
                            <div x-data="{ expanded: false }">
                                <p class="text-gray-400 leading-relaxed text-sm"
                                   :class="expanded ? '' : 'line-clamp-4'"
                                   id="bio-text">
                                    {{ $person['biography'] }}
                                </p>
                                @if(strlen($person['biography']) > 400)
                                    <button @click="expanded = !expanded"
                                            class="text-rose-400 text-sm mt-1 hover:underline">
                                        <span x-show="!expanded">Devamını oku ↓</span>
                                        <span x-show="expanded">Daralt ↑</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gray-900 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-white">{{ $totalCount }}</p>
                            <p class="text-gray-500 text-xs">Toplam İçerik</p>
                        </div>
                        <div class="bg-gray-900 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-yellow-400">
                                {{ !empty($allCredits) ? number_format(array_sum(array_map(fn($m) => $m['vote_average'] ?? 0, $allCredits)) / max(1, count($allCredits)), 1) : '—' }}
                            </p>
                            <p class="text-gray-500 text-xs">Ort. Puan</p>
                        </div>
                        <div class="bg-gray-900 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-white">{{ $person['popularity'] ? number_format($person['popularity'], 0) : '—' }}</p>
                            <p class="text-gray-500 text-xs">Popülerlik</p>
                        </div>
                        <div class="bg-gray-900 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-white">{{ $yearsActive ?? '—' }}</p>
                            <p class="text-gray-500 text-xs">Aktif Yıl</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filmography --}}
            <div>
                <h2 class="text-2xl font-bold text-white mb-6">
                    🎬 Filmografi
                    <span class="text-gray-500 text-lg font-normal ml-2">{{ $totalCount }} içerik</span>
                </h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                    @foreach($allCredits as $movie)
                        @php $isTV = ($movie['media_type'] ?? 'movie') === 'tv'; @endphp
                        <a href="{{ $isTV ? dizi_url($movie['id'], $movie['name'] ?? '') : film_url($movie['id'], $movie['title'] ?? '') }}" class="group block">
                            <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                                @if($movie['poster_path'])
                                    <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                         alt="{{ $movie['title'] ?? $movie['name'] ?? '' }}"
                                         class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">{{ $isTV ? '📺' : '🎬' }}</div>
                                @endif
                                <div class="absolute top-2 left-2 flex gap-1">
                                    @if($isTV)<span class="px-1.5 py-0.5 bg-purple-600/80 rounded text-[10px] text-white">Dizi</span>@endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                                    <div class="flex items-center gap-1 text-yellow-400 text-sm">
                                        ★ {{ number_format($movie['vote_average'] ?? 0, 1) }}
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-rose-400 transition">
                                {{ $movie['title'] ?? $movie['name'] ?? '' }}
                            </h3>
                            <p class="text-gray-500 text-xs">
                                {{ isset($movie['release_date']) ? substr($movie['release_date'], 0, 4) : (isset($movie['first_air_date']) ? substr($movie['first_air_date'], 0, 4) : '—') }}
                                @if(isset($movie['character']) && $movie['character'])
                                    · {{ $movie['character'] }}
                                @endif
                                @if(isset($movie['job']) && $movie['job'])
                                    · {{ $movie['job'] }}
                                @endif
                            </p>
                        </a>
                    @endforeach
                </div>

                @if(empty($allCredits))
                    <div class="text-center py-16 text-gray-500">
                        <p class="text-4xl mb-4">🎬</p>
                        <p>Henüz içerik bulunamadı.</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="text-center">
                <span class="text-6xl block mb-4">👤</span>
                <h1 class="text-2xl text-white font-bold mb-2">Kişi bulunamadı</h1>
                <a href="{{ url('/') }}" class="inline-block mt-4 px-6 py-2 bg-rose-600 text-white rounded-full hover:bg-rose-500 transition">
                    Ana Sayfaya Dön
                </a>
            </div>
        </div>
    @endif
</div>
