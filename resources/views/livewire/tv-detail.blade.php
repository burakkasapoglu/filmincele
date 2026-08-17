<div class="min-h-screen bg-gray-950">
    @if($series)
        @php
        $ldImage = $series['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $series['poster_path'] : null;
        @endphp
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org', '@type' => 'TVSeries',
            'name' => $series['name'] ?? '', 'description' => $series['overview'] ?? '',
            'startDate' => $series['first_air_date'] ?? '', 'image' => $ldImage,
            'numberOfSeasons' => $series['number_of_seasons'] ?? 0, 'numberOfEpisodes' => $series['number_of_episodes'] ?? 0,
            'genre' => !empty($series['genres']) ? array_column($series['genres'], 'name') : [],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
        <div class="relative h-[50vh] min-h-[350px]">
            @if($series['backdrop_path'])
                <img src="https://image.tmdb.org/t/p/w1280{{ $series['backdrop_path'] }}" class="w-full h-full object-cover" fetchpriority="high" alt="{{ $series['name'] }}">
            @else
                <div class="w-full h-full bg-gray-900"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/70 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <div class="max-w-7xl mx-auto flex gap-6 items-end">
                    @if($series['poster_path'])
                        <img src="https://image.tmdb.org/t/p/w342{{ $series['poster_path'] }}" class="w-40 rounded-xl shadow-2xl hidden md:block" alt="{{ $series['name'] }} posteri">
                    @endif
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-white mb-1">{{ $series['name'] }}</h1>
                        <div class="flex items-center gap-3 text-sm text-gray-300 flex-wrap">
                            @if($series['first_air_date'])
                                <span>{{ substr($series['first_air_date'], 0, 4) }}</span> <span>·</span>
                            @endif
                            @if($series['number_of_seasons'])
                                <span>{{ $series['number_of_seasons'] }} sezon</span> <span>·</span>
                            @endif
                            @if($series['number_of_episodes'])
                                <span>{{ $series['number_of_episodes'] }} bölüm</span> <span>·</span>
                            @endif
                            <span class="text-yellow-400">★ {{ number_format($series['vote_average'] ?? 0, 1) }}</span>
                        </div>
                        <div class="flex gap-2 mt-3 flex-wrap">
                            @foreach($series['genres'] ?? [] as $genre)
                                @php $moodSlug = config('genre-mood-map')[$genre['id']] ?? null; @endphp
                                @if($moodSlug)
                                    <a href="{{ url('/mod/' . $moodSlug . '?mediaType=tv') }}" class="px-3 py-1 bg-white/10 rounded-full text-xs text-white/80 hover:bg-rose-600/30 hover:text-rose-400 transition">
                                        {{ $genre['name'] }}
                                    </a>
                                @else
                                    <span class="px-3 py-1 bg-white/10 rounded-full text-xs text-white/80">{{ $genre['name'] }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    @if($series['overview'])
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Özet</h2>
                            <div x-data="{ expanded: false }">
                                <p class="text-gray-300 leading-relaxed text-sm" :class="expanded ? '' : 'line-clamp-4'">
                                    {{ $series['overview'] }}
                                </p>
                                @if(strlen($series['overview']) > 300)
                                    <button @click="expanded = !expanded" class="text-rose-400 text-sm mt-1 hover:underline">
                                        <span x-show="!expanded">Devamını oku ↓</span>
                                        <span x-show="expanded">Daralt ↑</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Directors --}}
                    @if($credits && !empty($credits['crew']))
                        @php $creators = array_filter($credits['crew'], fn($c) => in_array($c['job'] ?? '', ['Director', 'Executive Producer', 'Creator'])); @endphp
                        @if(!empty($creators))
                            <div>
                                <h2 class="text-xl font-semibold text-white mb-3">Yaratıcı Ekip</h2>
                                <div class="flex gap-3 flex-wrap">
                                    @foreach(array_slice($creators, 0, 6) as $c)
                                        <a href="{{ kisi_url($c['id'], $c['name']) }}" class="flex items-center gap-3 bg-gray-900 rounded-xl p-3 hover:bg-gray-800 transition group">
                                            <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-800 flex-shrink-0">
                                                @if($c['profile_path'])
                                                    <img src="https://image.tmdb.org/t/p/w185{{ $c['profile_path'] }}" class="w-full h-full object-cover" loading="lazy" alt="{{ $c['name'] }}">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-600">🎬</div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-white text-sm font-medium group-hover:text-rose-400 transition">{{ $c['name'] }}</p>
                                                <p class="text-gray-500 text-xs">{{ $c['job'] }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Recommendations --}}
                    @if(!empty($recommendations))
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Benzer Diziler</h2>
                            <div class="flex gap-3 overflow-x-auto pb-2">
                                @foreach(array_slice($recommendations, 0, 10) as $rec)
                                    <a href="{{ dizi_url($rec['id'], $rec['name'] ?? $rec['title'] ?? '') }}" class="flex-shrink-0 w-28 group">
                                        <div class="aspect-[2/3] rounded-lg overflow-hidden bg-gray-800 mb-2">
                                            @if($rec['poster_path'] ?? null)
                                                <img src="https://image.tmdb.org/t/p/w185{{ $rec['poster_path'] }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $rec['name'] ?? '' }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-2xl text-gray-600">📺</div>
                                            @endif
                                        </div>
                                        <p class="text-white text-xs font-medium truncate group-hover:text-rose-400 transition">{{ $rec['name'] ?? $rec['title'] ?? '' }}</p>
                                        <p class="text-gray-500 text-[10px]">★ {{ number_format($rec['vote_average'] ?? 0, 1) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Seasons --}}
                    @if(!empty($seasons) && count(array_filter($seasons, fn($s) => ($s['season_number'] ?? 0) > 0)) > 0)
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Sezonlar</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach($seasons as $season)
                                    @if(($season['season_number'] ?? 0) > 0)
                                        <div class="bg-gray-900 rounded-xl p-4 border border-gray-800/50">
                                            <div class="aspect-[2/3] rounded-lg overflow-hidden bg-gray-800 mb-3">
                                                @if($season['poster_path'])
                                                    <img src="https://image.tmdb.org/t/p/w342{{ $season['poster_path'] }}" class="w-full h-full object-cover" loading="lazy" alt="{{ $season['name'] }}">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-600">📺</div>
                                                @endif
                                            </div>
                                            <p class="text-white text-sm font-medium">Sezon {{ $season['season_number'] }}</p>
                                            <p class="text-gray-500 text-xs">{{ $season['episode_count'] ?? 0 }} bölüm</p>
                                            @if($season['air_date'])
                                                <p class="text-gray-600 text-xs">{{ substr($season['air_date'], 0, 4) }}</p>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Trailer --}}
                    @if($trailerUrl)
                        <div><h2 class="text-xl font-semibold text-white mb-3">Fragman</h2>
                            <div class="aspect-video rounded-xl overflow-hidden"><iframe src="{{ $trailerUrl }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe></div>
                        </div>
                    @endif

                    {{-- Cast --}}
                    @if($credits && !empty($credits['cast']))
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Oyuncular</h2>
                            <div class="flex gap-3 overflow-x-auto pb-2">
                                @foreach(array_slice($credits['cast'], 0, 15) as $cast)
                                    <a href="{{ kisi_url($cast['id'], $cast['name']) }}" class="flex-shrink-0 w-20 text-center group">
                                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-800 mx-auto mb-1.5">
                                            @if($cast['profile_path'])
                                                <img src="https://image.tmdb.org/t/p/w185{{ $cast['profile_path'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300" loading="lazy" alt="{{ $cast['name'] }}">
                                            @else <div class="w-full h-full flex items-center justify-center text-xl text-gray-600">👤</div> @endif
                                        </div>
                                        <p class="text-white text-xs font-medium truncate group-hover:text-rose-400 transition">{{ $cast['name'] }}</p>
                                        <p class="text-gray-500 text-[10px] truncate">{{ $cast['character'] ?? '' }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-5">
                    <div class="bg-gray-900 rounded-xl p-5 space-y-3">
                        <h3 class="text-white font-semibold">Bilgiler</h3>
                        @if($series['status'])<div class="flex justify-between text-sm"><span class="text-gray-400">Durum</span><span class="text-white">{{ $series['status'] === 'Returning Series' ? 'Devam Ediyor' : ($series['status'] === 'Ended' ? 'Sona Erdi' : $series['status']) }}</span></div>@endif
                        @if($series['first_air_date'])<div class="flex justify-between text-sm"><span class="text-gray-400">İlk Yayın</span><span class="text-white">{{ \Carbon\Carbon::parse($series['first_air_date'])->format('d.m.Y') }}</span></div>@endif
                        @if($series['number_of_seasons'])<div class="flex justify-between text-sm"><span class="text-gray-400">Sezon</span><span class="text-white">{{ $series['number_of_seasons'] }}</span></div>@endif
                        @if($series['number_of_episodes'])<div class="flex justify-between text-sm"><span class="text-gray-400">Bölüm</span><span class="text-white">{{ $series['number_of_episodes'] }}</span></div>@endif
                        @if($series['episode_run_time'] ?? [])<div class="flex justify-between text-sm"><span class="text-gray-400">Süre</span><span class="text-white">{{ $series['episode_run_time'][0] ?? '—' }} dk</span></div>@endif
                        @if($series['vote_average'])<div class="flex justify-between text-sm"><span class="text-gray-400">TMDB Puanı</span><span class="text-yellow-400">★ {{ number_format($series['vote_average'], 1) }}</span></div>@endif
                    </div>

                    {{-- Puanlama --}}
                    <div class="bg-gray-900 rounded-xl p-5">
                        <h3 class="text-white font-semibold mb-4">Puanla</h3>
                        @auth
                            <livewire:rating-stars :movie-id="$tmdbId" :media-type="'tv'" />
                        @else
                            <p class="text-gray-400 text-sm">Puan vermek için <a href="{{ url('/giris') }}" class="text-rose-400 underline underline-offset-4 decoration-rose-400/60 hover:text-rose-300">giriş yapın</a>.</p>
                        @endauth
                    </div>

                    {{-- Listeye Ekle --}}
                    <div class="bg-gray-900 rounded-xl p-5">
                        <h3 class="text-white font-semibold mb-4">Listeye Ekle</h3>
                        @auth
                            <livewire:watchlist-button :tmdb-id="$tmdbId" :media-type="'tv'" />
                        @else
                            <p class="text-gray-400 text-sm">Liste oluşturmak için <a href="{{ url('/giris') }}" class="text-rose-400 underline underline-offset-4 decoration-rose-400/60 hover:text-rose-300">giriş yapın</a>.</p>
                        @endauth
                    </div>

                    {{-- Watch Providers --}}
                    @php
                        $trData = $watchProviders['results']['TR'] ?? null;
                        $trStream = $trData['flatrate'] ?? [];
                        $trRent = $trData['rent'] ?? [];
                        $trBuy = $trData['buy'] ?? [];
                        $hasTR = !empty($trStream) || !empty($trRent) || !empty($trBuy);
                    @endphp
                    @if($hasTR)
                        <div class="bg-gray-900 rounded-xl p-5">
                            <h3 class="text-white font-semibold mb-3">🇹🇷 Nereden İzlenir?</h3>
                            @if(!empty($trStream))
                                <div class="mb-3">
                                    <span class="text-gray-500 text-xs uppercase tracking-wide">Abonelikle İzle</span>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($trStream as $p)
                                            <a href="{{ url('/platform/' . $p['provider_id'] . '/' . \Illuminate\Support\Str::slug($p['provider_name'])) }}"
                                               class="flex items-center gap-2 bg-gray-800 rounded-lg px-3 py-2 hover:bg-gray-700 transition group">
                                                @if($p['logo_path'])
                                                    <img src="https://image.tmdb.org/t/p/w45{{ $p['logo_path'] }}" class="w-6 h-6 rounded object-contain" loading="lazy" alt="{{ $p['provider_name'] ?? '' }}">
                                                @endif
                                                <span class="text-white text-xs group-hover:text-rose-400 transition">{{ $p['provider_name'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Production Companies --}}
                    @if(!empty($series['production_companies']))
                        <div class="bg-gray-900 rounded-xl p-5">
                            <h3 class="text-white font-semibold mb-2">Yapım Şirketi</h3>
                            <div class="space-y-1.5">
                                @foreach($series['production_companies'] as $c)
                                    <a href="{{ url('/sirket/' . $c['id'] . '/' . \Illuminate\Support\Str::slug($c['name'])) }}"
                                       class="flex items-center gap-2 text-sm text-white hover:text-rose-400 transition">
                                        @if($c['logo_path'])
                                            <img src="https://image.tmdb.org/t/p/w92{{ $c['logo_path'] }}" class="w-6 h-6 rounded object-contain" loading="lazy" alt="{{ $c['name'] }}">
                                        @endif
                                        {{ $c['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Countries --}}
                    @if(!empty($series['production_countries']))
                        <div class="bg-gray-900 rounded-xl p-5">
                            <h3 class="text-white font-semibold mb-2">Ülke</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($series['production_countries'] as $c)
                                    <a href="{{ url('/ulke/' . $c['iso_3166_1'] . '/' . \Illuminate\Support\Str::slug($c['name'])) }}"
                                       class="px-3 py-1 bg-gray-800 rounded-full text-xs text-white hover:bg-gray-700 hover:text-rose-400 transition">
                                        {{ $c['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="flex items-center justify-center min-h-[60vh]"><div class="text-center"><span class="text-6xl block mb-4">📺</span><h1 class="text-2xl text-white font-bold mb-2">Dizi bulunamadı</h1><a href="{{ url('/') }}" class="inline-block mt-4 px-6 py-2 bg-rose-600 text-white rounded-full hover:bg-rose-500 transition">Ana Sayfaya Dön</a></div></div>
    @endif
</div>
