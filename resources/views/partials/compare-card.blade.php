@php use Illuminate\Support\Str; $runtime = isset($m['runtime']) && $m['runtime'] > 0 ? floor($m['runtime']/60).'s '.($m['runtime']%60).'dk' : '—'; @endphp
<div class="bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden">
    <div class="h-44 relative">
        @if($m['backdrop_path'])
            <img src="https://image.tmdb.org/t/p/w780{{ $m['backdrop_path'] }}" class="w-full h-full object-cover" alt="{{ $m[\'title\'] ?? \'\' }}">
        @else <div class="w-full h-full bg-gray-800"></div> @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900"></div>
        <button wire:click="{{ $clear }}" class="absolute top-3 right-3 w-8 h-8 bg-black/50 hover:bg-red-600 rounded-full flex items-center justify-center text-white text-sm transition">✕</button>
    </div>
    <div class="p-5 -mt-14 relative">
        <div class="flex gap-4 items-end mb-4">
            @if($m['poster_path'])<img src="https://image.tmdb.org/t/p/w185{{ $m['poster_path'] }}" class="w-24 rounded-xl shadow-xl flex-shrink-0" alt="{{ $m[\'title\'] ?? \'\' }} posteri">@endif
            <div>
                <h2 class="text-xl font-bold text-white">{{ $m['title'] }}</h2>
                @if(($m['original_title'] ?? '') !== ($m['title'] ?? ''))
                    <p class="text-gray-500 text-xs">{{ $m['original_title'] }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
            <div class="bg-gray-800/50 rounded-xl p-3"><p class="text-gray-500 text-[10px] uppercase">Yıl</p><p class="text-white text-sm font-medium">{{ substr($m['release_date'] ?? '—', 0, 4) }}</p></div>
            <div class="bg-gray-800/50 rounded-xl p-3"><p class="text-gray-500 text-[10px] uppercase">Süre</p><p class="text-white text-sm font-medium">{{ $runtime }}</p></div>
            <div class="bg-gray-800/50 rounded-xl p-3"><p class="text-gray-500 text-[10px] uppercase">IMDb</p><p class="text-yellow-400 text-sm font-bold">★ {{ number_format($m['vote_average'] ?? 0, 1) }} <span class="text-gray-500 text-xs font-normal">({{ number_format($m['vote_count'] ?? 0) }})</span></p></div>
            @if($m['budget'] ?? 0 > 0)<div class="bg-gray-800/50 rounded-xl p-3"><p class="text-gray-500 text-[10px] uppercase">Bütçe</p><p class="text-white text-sm font-medium">${{ number_format($m['budget']) }}</p></div>@endif
            @if($m['revenue'] ?? 0 > 0)<div class="bg-gray-800/50 rounded-xl p-3"><p class="text-gray-500 text-[10px] uppercase">Hasılat</p><p class="text-white text-sm font-medium">${{ number_format($m['revenue']) }}</p></div>@endif
            @if($m['popularity'] ?? 0 > 0)<div class="bg-gray-800/50 rounded-xl p-3"><p class="text-gray-500 text-[10px] uppercase">Popülerlik</p><p class="text-white text-sm font-medium">{{ number_format($m['popularity']) }}</p></div>@endif
        </div>

        <div class="flex gap-1.5 mb-4 flex-wrap">
            @foreach($m['genres'] ?? [] as $g)<a href="{{ url('/mod/' . (config('genre-mood-map')[$g['id']] ?? 'dram')) }}" class="px-2 py-0.5 bg-gray-800 rounded-full text-xs text-gray-300 hover:text-rose-400 transition">{{ $g['name'] }}</a>@endforeach
        </div>

        @if($m['overview'])
            <div class="mb-4"><p class="text-gray-500 text-[10px] uppercase mb-1">Özet</p><p class="text-gray-400 text-sm leading-relaxed line-clamp-4">{{ $m['overview'] }}</p></div>
        @endif

        @php $directors = array_filter($m['credits']['crew'] ?? [], fn($c) => ($c['job'] ?? '') === 'Director'); @endphp
        @if(!empty($directors))
            <div class="mb-3"><p class="text-gray-500 text-[10px] uppercase mb-1">Yönetmen</p>
                @foreach($directors as $d)
                    <a href="{{ kisi_url($d['id'], $d['name']) }}" class="inline-flex items-center gap-1.5 bg-gray-800/50 rounded-lg px-2.5 py-1 hover:bg-gray-800 transition group mr-1.5 mb-1">
                        <div class="w-5 h-5 rounded-full overflow-hidden bg-gray-700 flex-shrink-0">@if($d['profile_path'])<img src="https://image.tmdb.org/t/p/w92{{ $d['profile_path'] }}" class="w-full h-full object-cover" alt="{{ $d[\'name\'] }}">@else<span class="text-[10px]">🎬</span>@endif</div>
                        <span class="text-white text-xs group-hover:text-rose-400 transition">{{ $d['name'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        @if(!empty($m['credits']['cast'] ?? []))
            <div><p class="text-gray-500 text-[10px] uppercase mb-1">Oyuncular</p><p class="text-gray-400 text-xs leading-relaxed">
                @foreach(array_slice($m['credits']['cast'], 0, 8) as $c)
                    <a href="{{ kisi_url($c['id'], $c['name']) }}" class="hover:text-rose-400 transition">{{ $c['name'] }}</a>{{ !$loop->last ? ' · ' : '' }}
                @endforeach
            </p></div>
        @endif
    </div>
</div>
