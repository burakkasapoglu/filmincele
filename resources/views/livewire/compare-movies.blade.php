<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-white text-center mb-8">🎬 Film Karşılaştır</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Film 1 --}}
        <div>
            @if(!$movie1)
                <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6 text-center">
                    <div class="text-5xl mb-4">🎬</div>
                    <p class="text-gray-400 text-sm mb-4">Karşılaştırmak için bir film seç</p>
                    <input type="text" wire:model.live.debounce.300ms="search1" placeholder="Film ara..."
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 text-center">
                    @foreach($suggestions1 as $s)
                        <button wire:click="select1({{ $s['id'] }})"
                                class="w-full text-left px-4 py-3 hover:bg-gray-800 rounded-lg flex items-center gap-3 transition border-b border-gray-800 last:border-0">
                            <div class="w-10 h-14 rounded-lg overflow-hidden bg-gray-700 flex-shrink-0">
                                @if($s['poster_path'])<img src="https://image.tmdb.org/t/p/w92{{ $s['poster_path'] }}" class="w-full h-full object-cover" alt="{{ $s['title'] ?? '' }}">@endif
                            </div>
                            <div class="min-w-0"><p class="text-white text-sm font-medium truncate">{{ $s['title'] }}</p><p class="text-gray-500 text-xs">{{ substr($s['release_date'] ?? '—', 0, 4) }} · ★ {{ number_format($s['vote_average'] ?? 0, 1) }}</p></div>
                        </button>
                    @endforeach
                </div>
            @else
                @include('partials.compare-card', ['m' => $movie1, 'clear' => 'clear1'])
            @endif
        </div>

        {{-- Film 2 --}}
        <div>
            @if(!$movie2)
                <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6 text-center">
                    <div class="text-5xl mb-4">🎬</div>
                    <p class="text-gray-400 text-sm mb-4">Karşılaştırmak için bir film seç</p>
                    <input type="text" wire:model.live.debounce.300ms="search2" placeholder="Film ara..."
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 text-center">
                    @foreach($suggestions2 as $s)
                        <button wire:click="select2({{ $s['id'] }})"
                                class="w-full text-left px-4 py-3 hover:bg-gray-800 rounded-lg flex items-center gap-3 transition border-b border-gray-800 last:border-0">
                            <div class="w-10 h-14 rounded-lg overflow-hidden bg-gray-700 flex-shrink-0">
                                @if($s['poster_path'])<img src="https://image.tmdb.org/t/p/w92{{ $s['poster_path'] }}" class="w-full h-full object-cover" alt="{{ $s['title'] ?? '' }}">@endif
                            </div>
                            <div class="min-w-0"><p class="text-white text-sm font-medium truncate">{{ $s['title'] }}</p><p class="text-gray-500 text-xs">{{ substr($s['release_date'] ?? '—', 0, 4) }} · ★ {{ number_format($s['vote_average'] ?? 0, 1) }}</p></div>
                        </button>
                    @endforeach
                </div>
            @else
                @include('partials.compare-card', ['m' => $movie2, 'clear' => 'clear2'])
            @endif
        </div>
    </div>
</div>
