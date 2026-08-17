<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center gap-2 mb-4">
            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Bugün Sinemada</h2>
            <span class="text-gray-400 text-xs">{{ $todayDate }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Birthdays: Dün / Bugün / Yarın --}}
            <div>
                @if(!empty($birthdayGroups))
                    @foreach($birthdayGroups as $group)
                        <div class="mb-3 last:mb-0">
                            <span class="inline-block text-[10px] uppercase tracking-wider mb-1.5
                                {{ $group['label'] === 'Bugün' ? 'text-rose-400 font-semibold' : 'text-gray-500' }}">
                                🎈 {{ $group['label'] }} Doğanlar
                            </span>
                            <div class="flex gap-2 overflow-x-auto pb-1">
                                @foreach($group['people'] as $person)
                                    <a href="{{ kisi_url($person['tmdb_id'], $person['name']) }}"
                                       class="flex-shrink-0 flex items-center gap-2 bg-gray-900 rounded-lg pl-2 pr-3 py-2 hover:bg-gray-800 transition group
                                              {{ $group['label'] === 'Bugün' ? 'ring-1 ring-rose-600/30' : '' }}">
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-800 flex-shrink-0">
                                            @if($person['profile_path'])
                                                <img src="https://image.tmdb.org/t/p/w92{{ $person['profile_path'] }}"
                                                     alt="{{ $person['name'] }}"
                                                     class="w-full h-full object-cover" loading="lazy">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-sm text-gray-400">👤</div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-white text-xs font-medium truncate max-w-[100px] group-hover:text-rose-400 transition">
                                                {{ $person['name'] }}
                                            </p>
                                            <p class="text-gray-500 text-[10px]">
                                                {{ $person['age'] ?? '—' }} yaş
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-400 text-xs py-3">🎂 Yakın zamanda doğan kimse yok.</p>
                @endif
            </div>

            {{-- Movie Anniversaries --}}
            <div class="lg:border-l lg:border-gray-800 lg:pl-5">
                <span class="inline-block text-[10px] uppercase tracking-wider text-gray-500 mb-1.5">
                    🎬 Bu Tarihte Vizyona Girenler
                </span>
                @if(!empty($anniversaryMovies))
                    <div class="flex gap-3 overflow-x-auto pb-1">
                        @foreach($anniversaryMovies as $movie)
                            <a href="{{ url('/film/' . $movie['id'] . '-' . \Illuminate\Support\Str::slug($movie['title'] ?? '')) }}"
                               class="flex-shrink-0 w-16 group">
                                <div class="aspect-[2/3] rounded-md overflow-hidden bg-gray-800 mb-1">
                                    @if($movie['poster_path'])
                                        <img src="https://image.tmdb.org/t/p/w154{{ $movie['poster_path'] }}"
                                             alt="{{ $movie['title'] ?? $movie['name'] ?? 'Film posteri' }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                             loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xl text-gray-400">🎬</div>
                                    @endif
                                </div>
                                <p class="text-gray-300 text-[10px] leading-tight line-clamp-2 group-hover:text-rose-400 transition">
                                    {{ $movie['title'] }}
                                </p>
                                <p class="text-gray-400 text-[10px]">
                                    {{ substr($movie['release_date'] ?? '—', 0, 4) }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-xs py-3">Bu tarihe ait içerik bulunamadı.</p>
                @endif
            </div>

        </div>
    </div>
</div>
