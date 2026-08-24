<div class="py-12">
    <h2 class="text-center text-2xl font-bold text-white mb-2">Nasıl hissediyorsun?</h2>
    <p class="text-center text-gray-400 mb-8">Ruh haline göre sana özel film önerileri seçelim</p>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4 max-w-6xl mx-auto px-4">
        @foreach($moods as $slug => $mood)
@php
    $moodStills = [
        'romantik' => ['img' => 'zywtNiaZ9r7azrcNdl2j0jOgrkw', 'film' => 'Terminal'],
        'macera' => ['img' => 'tlm8UkiQsitc8rSuIAscQDCnP8d', 'film' => 'Matrix'],
        'komedi' => ['img' => 'auO1IdZSpZf8nvGGvRnEdHNdp4r', 'film' => '100 Kız'],
        'gerilim' => ['img' => 'mufF1aYvwdpKerhq5R1YrVcbJLY', 'film' => 'Sapık'],
        'bilimkurgu' => ['img' => 'vgnoBSVzWAV9sNQUORaDGvDp7wx', 'film' => 'Yıldızlararası'],
        'dram' => ['img' => 'amZavErrjrdgDwIYJwULtzxzkJj', 'film' => 'Yeşil Yol'],
        'gizem' => ['img' => '8ZTVqvKDQ8emSGUEMjsS4yHAwrp', 'film' => 'Başlangıç'],
        'muzikal' => ['img' => 'eMgflonfRY89Zj4LZykJKPpG4wg', 'film' => 'Gazap Üzümleri'],
        'aile' => ['img' => '3Rfvhy1Nl6sSGJwyjb0QiZzZYlB', 'film' => 'Oyuncak Hikayesi'],
        'aksiyon' => ['img' => '9FE5eD92WfVCiivM9Pq9GVSrlWk', 'film' => 'Kara Şövalye'],
        '+18' => ['img' => '9IIBboV7MCT0bTxzXHmWK1Hq558', 'film' => 'Batman Başlıyor'],
        'turk' => ['img' => 'inEy3A5OPgeYW4rjRiGycfEeQzA', 'film' => '7. Koğuştaki Mucize'],
    ];
    $still = $moodStills[$slug] ?? null;
    $isAdult = !empty($mood['adult']);
    $locked = $isAdult && (!Auth::check() || !Auth::user()->isAdult());
@endphp
            <button
                wire:click="{{ $locked ? '' : "selectMood('{$slug}')" }}"
                class="relative group rounded-2xl overflow-hidden text-left transition-all duration-300 cursor-pointer
                       aspect-[16/10]
                       {{ $selectedMood === $slug ? 'ring-4 ring-amber-400 scale-105' : 'hover:scale-105' }}
                       {{ $locked ? 'cursor-not-allowed' : '' }}"
                @if($locked)
                   onclick="window.location='{{ url('/giris') }}'"
                @endif
            >
                @if($still)
                    <img src="https://image.tmdb.org/t/p/w500/{{ $still['img'] }}.jpg"
                         alt="{{ $mood['label'] }}"
                         class="absolute inset-0 w-full h-full object-cover transition duration-500 {{ $selectedMood === $slug ? '' : 'group-hover:scale-110' }} {{ $locked ? 'grayscale opacity-40' : '' }}"
                         loading="lazy">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br {{ $mood['gradient'] }}"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent"></div>

                <div class="absolute inset-x-0 bottom-0 p-3">
                    <span class="text-white font-semibold text-sm block drop-shadow-lg">
                        {{ $mood['label'] }}
                        @if($locked)
                            🔒
                        @endif
                    </span>
                    <span class="text-white/60 text-[10px] block mt-0.5">{{ $mood['description'] }}</span>
                    @if($still && !$locked)
                        <span class="text-amber-400/80 text-[10px] block mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">🎥 {{ $still['film'] }}</span>
                    @endif
                    @if($locked)
                        <span class="text-white/60 text-[10px] block mt-1">Giriş yap & 18+</span>
                    @endif
                </div>
            </button>
        @endforeach
    </div>

    @if($selectedMood)
        <div class="mt-8 text-center">
            <a href="{{ url('/mod/' . $selectedMood) }}"
               class="inline-flex items-center px-8 py-3 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-full transition duration-200">
                Keşfet →
            </a>
        </div>
    @endif
</div>
