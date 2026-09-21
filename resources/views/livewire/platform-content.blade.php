<div class="max-w-7xl mx-auto px-4 py-8">
    @php
        $providerTitle = $provider['name'] ?? str_replace('-', ' ', $providerName);
        $color = $provider['color'] ?? '#F59E0B';
        $emoji = $provider['emoji'] ?? '📺';
    @endphp

    <div class="flex items-center gap-5 mb-6">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0"
             style="background: {{ $color }}22; border: 1px solid {{ $color }}40">
            {{ $emoji }}
        </div>
        <div class="min-w-0">
            <h1 class="text-3xl font-bold text-white">{{ $providerTitle }}</h1>
            <p class="text-gray-400 text-sm">Bu platformda Türkiye'de izlenebilen içerikler</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6">
        <button wire:click="switchTab('movie')"
                class="px-5 py-2 rounded-xl text-sm font-medium transition {{ $tab === 'movie' ? 'bg-amber-600 text-white' : 'bg-gray-900 border border-gray-800 text-gray-300 hover:text-white' }}">
            🎬 Filmler
        </button>
        <button wire:click="switchTab('tv')"
                class="px-5 py-2 rounded-xl text-sm font-medium transition {{ $tab === 'tv' ? 'bg-amber-600 text-white' : 'bg-gray-900 border border-gray-800 text-gray-300 hover:text-white' }}">
            📺 Diziler
        </button>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @forelse($items as $item)
            @php
                $title = $item['title'] ?? $item['name'] ?? '';
                $isTv = !empty($item['name']);
                $path = url(($isTv ? '/dizi/' : '/film/') . $item['id'] . '-' . \Illuminate\Support\Str::slug($title));
            @endphp
            <a href="{{ $path }}" class="cinema-card group block">
                <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-gray-800">
                    @if($item['poster_path'])
                        <img src="https://image.tmdb.org/t/p/w342{{ $item['poster_path'] }}"
                             alt="{{ $title }}"
                             class="w-full h-full object-cover transition duration-500 group-hover:scale-105"
                             loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-700">{{ $isTv ? '📺' : '🎬' }}</div>
                    @endif
                    @if($item['vote_average'] ?? 0)
                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-black/70 rounded-lg text-xs font-semibold text-amber-400">★ {{ number_format($item['vote_average'], 1) }}</span>
                    @endif
                </div>
                <h3 class="text-white text-sm font-medium mt-2 truncate group-hover:text-amber-400 transition">{{ $title }}</h3>
                <p class="text-gray-500 text-xs">{{ substr($item['first_air_date'] ?? $item['release_date'] ?? '—', 0, 4) }}</p>
            </a>
        @empty
            <div class="col-span-full bg-gray-900 rounded-2xl p-10 text-center text-gray-500">Bu sekmede içerik bulunamadı.</div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($totalPages > 1)
        <div class="flex items-center justify-center gap-2 mt-10 flex-wrap">
            @if($page > 1)
                <button wire:click="goToPage({{ $page - 1 }})" class="px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl text-sm text-gray-300 hover:text-white transition">← Önceki</button>
            @endif
            @php
                $s = max(1, $page - 2); $e = min($totalPages, $page + 2);
            @endphp
            @if($s > 1)
                <button wire:click="goToPage(1)" class="px-3 py-2 bg-gray-900 rounded-xl text-sm text-gray-400">1</button>
                @if($s > 2)<span class="text-gray-600 px-1">…</span>@endif
            @endif
            @for($p = $s; $p <= $e; $p++)
                <button wire:click="goToPage({{ $p }})" class="px-3 py-2 rounded-xl text-sm transition {{ $p === $page ? 'bg-amber-600 text-white font-semibold' : 'bg-gray-900 border border-gray-800 text-gray-400' }}">{{ $p }}</button>
            @endfor
            @if($e < $totalPages)
                @if($e < $totalPages - 1)<span class="text-gray-600 px-1">…</span>@endif
                <button wire:click="goToPage({{ $totalPages }})" class="px-3 py-2 bg-gray-900 rounded-xl text-sm text-gray-400">{{ $totalPages }}</button>
            @endif
            @if($page < $totalPages)
                <button wire:click="goToPage({{ $page + 1 }})" class="px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl text-sm text-gray-300 hover:text-white transition">Sonraki →</button>
            @endif
        </div>
    @endif

    {{-- Kullanici aliskanlik ipucu --}}
    @auth
        @php
            $top = app(\App\Services\PlatformHabitService::class)->getTopPlatform(Auth::user());
        @endphp
        @if($top && strtolower($top['name']) === strtolower($providerTitle))
            <div class="mt-8 bg-amber-600/10 border border-amber-600/30 rounded-2xl p-5">
                <p class="text-amber-400 font-medium text-sm">🎯 İzleme alışkanlığın: {{ $top['name'] }}</p>
                <p class="text-gray-400 text-xs mt-1">Listen ve puanlarına göre {{ $top['name'] }}'te {{ $top['count'] }} içeriğin var — bu platform sana iyi uyar!</p>
            </div>
        @endif
    @endauth
</div>