<div class="min-h-screen bg-gray-950">
    @if($movie)
        @php
        $ldImage = $movie['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : null;
        $ldJson = [
            '@context' => 'https://schema.org',
            '@type' => 'Movie',
            'name' => $movie['title'] ?? '',
            'alternateName' => $movie['original_title'] ?? '',
            'description' => $movie['overview'] ?? '',
            'dateCreated' => $movie['release_date'] ?? '',
            'image' => $ldImage,
            'url' => url()->current(),
            'sameAs' => ['https://filmincele.com'],
        ];
        if (!empty($movie['genres'])) {
            $ldJson['genre'] = array_column($movie['genres'], 'name');
        }
        if (!empty($movie['credits']['crew'] ?? [])) {
            foreach ($movie['credits']['crew'] as $c) {
                if ($c['job'] === 'Director') { $ldJson['director'] = ['@type' => 'Person', 'name' => $c['name']]; break; }
            }
        }
        $userRatings = $localMovie?->ratings ?? collect();
        $textReviews = $userRatings->filter(fn ($r) => trim((string) $r->review) !== '')->take(10)->values();
        if ($userRatings->count() > 0) {
            $ldJson['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $localMovie->avgRating(),
                'bestRating' => 10,
                'worstRating' => 1,
                'ratingCount' => $userRatings->count(),
            ];
        }
        if ($textReviews->count() > 0) {
            $ldJson['review'] = $textReviews->map(fn ($r) => [
                '@type' => 'Review',
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $r->rating, 'bestRating' => 10, 'worstRating' => 1],
                'author' => ['@type' => 'Person', 'name' => $r->user?->name ?? 'Filmincele kullanıcısı'],
                'datePublished' => $r->created_at?->toDateString(),
                'reviewBody' => $r->review,
            ])->all();
        }
        $trailerVideo = collect($videos ?? [])->first(fn ($v) => ($v['site'] ?? '') === 'YouTube' && ($v['type'] ?? '') === 'Trailer')
            ?? collect($videos ?? [])->first(fn ($v) => ($v['site'] ?? '') === 'YouTube');
        if ($trailerVideo) {
            $ldJson['trailer'] = [
                '@type' => 'VideoObject',
                'name' => ($movie['title'] ?? '') . ' — Fragman',
                'description' => \Illuminate\Support\Str::limit($movie['overview'] ?? '', 300),
                'thumbnailUrl' => ['https://img.youtube.com/vi/' . $trailerVideo['key'] . '/hqdefault.jpg'],
                'uploadDate' => $trailerVideo['published_at'] ?? ($movie['release_date'] ?? null),
                'embedUrl' => 'https://www.youtube.com/embed/' . $trailerVideo['key'],
            ];
        }
        @endphp
        <script type="application/ld+json">{!! json_encode($ldJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

        {{-- Hero Section --}}
        <div class="relative h-[60vh] min-h-[400px]">
            @if($movie['backdrop_path'])
                <img src="https://image.tmdb.org/t/p/w1280{{ $movie['backdrop_path'] }}"
                     alt="{{ $movie['title'] }}"
                     fetchpriority="high"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-900"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/70 to-transparent"></div>

            <div class="absolute bottom-0 left-0 right-0 p-8">
                <div class="max-w-7xl mx-auto flex gap-8 items-end">
                    @if($movie['poster_path'])
                        <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                             alt="{{ $movie['title'] }}"
                             class="w-48 rounded-xl shadow-2xl hidden md:block">
                    @endif
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold text-white mb-2">{{ $movie['title'] }}</h1>
                        @if($movie['title'] !== ($movie['original_title'] ?? ''))
                            <p class="text-gray-400 text-lg mb-3">{{ $movie['original_title'] }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-sm text-gray-300 flex-wrap">
                            @php $displayDate = $movie['tr_release_date'] ?? $movie['release_date'] ?? null; @endphp
                            @if($displayDate)
                                <span>{{ substr($displayDate, 0, 4) }}</span>
                                <span>·</span>
                            @endif
                            @if($movie['runtime'])
                                <span>{{ floor($movie['runtime'] / 60) }}s {{ $movie['runtime'] % 60 }}dk</span>
                                <span>·</span>
                            @endif
                            <span class="text-yellow-400">★ {{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                        </div>
                        <div class="flex gap-2 mt-4 flex-wrap">
                            @if($inCinemas)
                                <span class="px-3 py-1 bg-amber-500/20 border border-amber-500/40 rounded-full text-xs text-amber-400 font-semibold">🎟️ Sinemalarda</span>
                            @endif
                            @foreach($movie['genres'] ?? [] as $genre)
                                @php $moodSlug = config('genre-mood-map')[$genre['id']] ?? null; @endphp
                                @if($moodSlug)
                                    <a href="{{ url('/mod/' . $moodSlug) }}" class="px-3 py-1 bg-white/10 rounded-full text-xs text-white/80 hover:bg-rose-600/30 hover:text-rose-400 transition">
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

                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Overview --}}
                    @if($movie['overview'])
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Özet</h2>
                            <p class="text-gray-300 leading-relaxed">{{ $movie['overview'] }}</p>
                        </div>
                    @endif

                    {{-- Trailer --}}
                    @if($trailerUrl)
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Fragman</h2>
                            <div class="aspect-video rounded-xl overflow-hidden">
                                <iframe src="{{ $trailerUrl }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    @endif

                    {{-- Directors --}}
                    @if($credits && !empty($credits['crew']))
                        @php $directors = array_filter($credits['crew'], fn($c) => $c['job'] === 'Director'); @endphp
                        @if(!empty($directors))
                            <div>
                                <h2 class="text-xl font-semibold text-white mb-3">Yönetmen</h2>
                                <div class="flex gap-4 flex-wrap">
                                    @foreach($directors as $director)
                                        <a href="{{ kisi_url($director['id'], $director['name']) }}"
                                           class="flex items-center gap-3 bg-gray-900 rounded-xl p-3 hover:bg-gray-800 transition group">
                                            <div class="w-14 h-14 rounded-full overflow-hidden bg-gray-800 flex-shrink-0">
                                                @if($director['profile_path'])
                                                    <img src="https://image.tmdb.org/t/p/w185{{ $director['profile_path'] }}"
                                                         alt="{{ $director['name'] }}"
                                                         class="w-full h-full object-cover" loading="lazy">
                                                @else
                                                    <img src="{{ cult_poster('rec-' . $rec['id']) }}" alt="{{ $rec['title'] }}" class="w-full h-full object-cover opacity-60">
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-white text-sm font-medium group-hover:text-rose-400 transition">{{ $director['name'] }}</p>
                                                <p class="text-gray-500 text-xs">Yönetmen</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Cast --}}
                    @if($credits && !empty($credits['cast']))
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Oyuncular</h2>
                            <div class="flex gap-4 overflow-x-auto pb-4">
                                @foreach(array_slice($credits['cast'], 0, 20) as $cast)
                                    <a href="{{ kisi_url($cast['id'], $cast['name']) }}" class="flex-shrink-0 w-24 text-center group">
                                        <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-800 mx-auto mb-2">
                                            @if($cast['profile_path'])
                                                <img src="https://image.tmdb.org/t/p/w185{{ $cast['profile_path'] }}"
                                                     alt="{{ $cast['name'] }}"
                                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                                     loading="lazy">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-2xl text-gray-600">
                                                    👤
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-white text-xs font-medium truncate group-hover:text-rose-400 transition">{{ $cast['name'] }}</p>
                                        <p class="text-gray-500 text-xs truncate">{{ $cast['character'] ?? '' }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Recommendations --}}
                    @if($recommendations && !empty($recommendations))
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Benzer İçerikler</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach(array_slice($recommendations, 0, 8) as $rec)
                                    <a href="{{ url('/film/' . $rec['id'] . '-' . \Illuminate\Support\Str::slug($rec['title'] ?? '')) }}" class="group">
                                        <div class="aspect-[2/3] rounded-lg overflow-hidden bg-gray-800 mb-2">
                                            @if($rec['poster_path'])
                                                <img src="https://image.tmdb.org/t/p/w342{{ $rec['poster_path'] }}"
                                                     alt="{{ $rec['title'] }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                                     loading="lazy">
                                            @endif
                                        </div>
                                        <p class="text-white text-xs font-medium truncate group-hover:text-rose-400 transition">
                                            {{ $rec['title'] }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- User Reviews --}}
                    @if($textReviews->count() > 0)
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-3">Kullanıcı Yorumları</h2>
                            <div class="space-y-4">
                                @foreach($textReviews as $r)
                                    <div class="bg-gray-900 rounded-xl p-5">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-white text-sm font-medium">{{ $r->user?->name ?? 'Filmincele kullanıcısı' }}</span>
                                                <span class="text-yellow-400 text-xs">★ {{ $r->rating }}/10</span>
                                            </div>
                                            @if($r->created_at)
                                                <span class="text-gray-600 text-xs">{{ $r->created_at->format('d.m.Y') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-300 text-sm leading-relaxed">{{ $r->review }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">
                    {{-- Rating --}}
                    <div class="bg-gray-900 rounded-xl p-6">
                        <h3 class="text-white font-semibold mb-4">Puanla</h3>
                        @auth
                            <livewire:rating-stars :movie-id="$tmdbId" />
                        @else
                            <p class="text-gray-400 text-sm">
                                Puan vermek için <a href="{{ url('/giris') }}" class="text-rose-400 underline underline-offset-4 decoration-rose-400/60 hover:text-rose-300">giriş yapın</a>.
                            </p>
                        @endauth
                        @if($localMovie && $localMovie->ratings()->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-800">
                                <p class="text-gray-500 text-xs mb-2">Filmincele Kullanıcı Puanı</p>
                                <div class="flex items-end gap-2">
                                    <span class="text-3xl font-bold text-yellow-400">{{ number_format($localMovie->avgRating(), 1) }}</span>
                                    <span class="text-gray-500 text-sm mb-1">/10</span>
                                </div>
                                <p class="text-gray-600 text-xs mt-1">{{ $localMovie->ratings()->count() }} kullanıcı puanladı</p>
                                @php $dist = $localMovie->ratings()->selectRaw('rating, count(*) as count')->groupBy('rating')->pluck('count', 'rating'); $max = $dist->max() ?: 1; @endphp
                                <div class="mt-3 space-y-1">
                                    @for($i = 10; $i >= 1; $i--)
                                        @php $c = $dist[$i] ?? 0; $pct = ($c / $max) * 100; @endphp
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="text-gray-500 w-4 text-right">{{ $i }}</span>
                                            <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                                <div class="h-full bg-yellow-500/50 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="text-gray-600 w-6">{{ $c }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Watchlist --}}
                    <div class="bg-gray-900 rounded-xl p-6">
                        <h3 class="text-white font-semibold mb-4">Listeye Ekle</h3>
                        @auth
                            <livewire:watchlist-button :tmdb-id="$tmdbId" />
                        @else
                            <p class="text-gray-400 text-sm">
                                Liste oluşturmak için <a href="{{ url('/giris') }}" class="text-rose-400 underline underline-offset-4 decoration-rose-400/60 hover:text-rose-300">giriş yapın</a>.
                            </p>
                        @endauth
                    </div>

                    {{-- Info --}}
                    <div class="bg-gray-900 rounded-xl p-6 space-y-3">
                        <h3 class="text-white font-semibold">Bilgiler</h3>
                        @php $displayDate = $movie['tr_release_date'] ?? $movie['release_date'] ?? null; @endphp
                        @if($displayDate)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Çıkış Tarihi</span>
                                <span class="text-white">{{ \Carbon\Carbon::parse($displayDate)->format('d.m.Y') }}</span>
                            </div>
                        @endif
                        @if($movie['runtime'])
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Süre</span>
                                <span class="text-white">{{ floor($movie['runtime'] / 60) }}s {{ $movie['runtime'] % 60 }}dk</span>
                            </div>
                        @endif
                        @if($movie['vote_average'])
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">TMDB Puanı</span>
                                <span class="text-yellow-400">★ {{ number_format($movie['vote_average'], 1) }}</span>
                            </div>
                        @endif
                        @if($movie['budget'] ?? 0 > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Bütçe</span>
                                <span class="text-white">${{ number_format($movie['budget']) }}</span>
                            </div>
                        @endif
                        @if($movie['revenue'] ?? 0 > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Hasılat</span>
                                <span class="text-white">${{ number_format($movie['revenue']) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Production Companies --}}
                    @if(!empty($movie['production_companies']))
                        <div class="bg-gray-900 rounded-xl p-6">
                            <h3 class="text-white font-semibold mb-3">Yapım Şirketleri</h3>
                            <div class="space-y-2">
                                @foreach($movie['production_companies'] as $company)
                                    <a href="{{ url('/sirket/' . $company['id'] . '/' . \Illuminate\Support\Str::slug($company['name'])) }}"
                                       class="flex items-center gap-3 hover:bg-gray-800 rounded-lg p-2 -mx-2 transition group">
                                        <div class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            @if($company['logo_path'])
                                                <img src="https://image.tmdb.org/t/p/w92{{ $company['logo_path'] }}"
                                                     alt="{{ $company['name'] }}"
                                                     class="w-full h-full object-contain p-1"
                                                     loading="lazy">
                                            @else
                                                <span class="text-gray-600 text-lg">🏢</span>
                                            @endif
                                        </div>
                                        <span class="text-white text-sm group-hover:text-rose-400 transition">{{ $company['name'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Countries --}}
                    @if(!empty($movie['production_countries']))
                        <div class="bg-gray-900 rounded-xl p-6">
                            <h3 class="text-white font-semibold mb-3">Ülke</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($movie['production_countries'] as $country)
                                    <a href="{{ url('/ulke/' . $country['iso_3166_1'] . '/' . \Illuminate\Support\Str::slug($country['name'])) }}"
                                       class="px-3 py-1 bg-gray-800 rounded-full text-sm text-white hover:bg-gray-700 hover:text-rose-400 transition">
                                        {{ $country['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- In Cinemas --}}
                    @if($inCinemas)
                        <div class="bg-gray-900 rounded-xl p-6">
                            <h3 class="text-white font-semibold mb-3">🎟️ Nereden İzlenir?</h3>
                            <a href="{{ url('/vizyonda') }}"
                               class="flex items-center gap-3 bg-amber-600/10 border border-amber-600/30 rounded-lg px-4 py-3 hover:bg-amber-600/20 transition group">
                                <span class="text-2xl">🍿</span>
                                <div>
                                    <p class="text-amber-400 font-medium text-sm group-hover:text-amber-300 transition">Şu Anda Sinemalarda</p>
                                    <p class="text-gray-400 text-xs mt-0.5">Bu film vizyonda — yakın sinemalardan bilet alabilirsiniz</p>
                                </div>
                                <span class="ml-auto text-amber-400/60 group-hover:translate-x-1 transition">→</span>
                            </a>
                        </div>
                    @endif

                    {{-- Watch Providers --}}
                    @if(!empty($watchProviders))
                        @php
                            $trStream = $watchProviders['TR']['stream'] ?? [];
                            $trRent = $watchProviders['TR']['rent'] ?? [];
                            $trBuy = $watchProviders['TR']['buy'] ?? [];
                            $hasTR = !empty($trStream) || !empty($trRent) || !empty($trBuy);
                        @endphp
                        @if($hasTR)
                            <div class="bg-gray-900 rounded-xl p-6">
                                <h3 class="text-white font-semibold mb-3">🇹🇷 {{ $inCinemas ? 'Ayrıca İzleme Seçenekleri' : 'Nereden İzlenir?' }}</h3>

                                @if(!empty($trStream))
                                    <div class="mb-3">
                                        <span class="text-gray-500 text-xs uppercase tracking-wide">Abonelikle İzle</span>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($trStream as $p)
                                                <a href="{{ url('/platform/' . $p['provider_id'] . '/' . \Illuminate\Support\Str::slug($p['provider_name'])) }}"
                                                   class="flex items-center gap-2 bg-gray-800 rounded-lg px-3 py-2 hover:bg-gray-700 transition group">
                                                    @if($p['logo_path'])
                                                        <img src="https://image.tmdb.org/t/p/w45{{ $p['logo_path'] }}"
                                                             alt="{{ $p['provider_name'] }}"
                                                             class="w-6 h-6 rounded object-contain"
                                                             loading="lazy">
                                                    @endif
                                                    <span class="text-white text-xs group-hover:text-rose-400 transition">{{ $p['provider_name'] }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($trRent))
                                    <div class="mb-3">
                                        <span class="text-gray-500 text-xs uppercase tracking-wide">Kiralama</span>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($trRent as $p)
                                                <a href="{{ url('/platform/' . $p['provider_id'] . '/' . \Illuminate\Support\Str::slug($p['provider_name'])) }}"
                                                   class="px-3 py-1 bg-gray-800 rounded-full text-xs text-white hover:bg-gray-700 hover:text-rose-400 transition">
                                                    {{ $p['provider_name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($trBuy))
                                    <div>
                                        <span class="text-gray-500 text-xs uppercase tracking-wide">Satın Alma</span>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($trBuy as $p)
                                                <a href="{{ url('/platform/' . $p['provider_id'] . '/' . \Illuminate\Support\Str::slug($p['provider_name'])) }}"
                                                   class="px-3 py-1 bg-gray-800 rounded-full text-xs text-white hover:bg-gray-700 hover:text-rose-400 transition">
                                                    {{ $p['provider_name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="text-center">
                <span class="text-6xl block mb-4">🎬</span>
                <h1 class="text-2xl text-white font-bold mb-2">Film bulunamadı</h1>
                <p class="text-gray-400">Bu film mevcut değil veya kaldırılmış olabilir.</p>
                <a href="{{ url('/') }}" class="inline-block mt-4 px-6 py-2 bg-rose-600 text-white rounded-full hover:bg-rose-500 transition">
                    Ana Sayfaya Dön
                </a>
            </div>
        </div>
    @endif
</div>
