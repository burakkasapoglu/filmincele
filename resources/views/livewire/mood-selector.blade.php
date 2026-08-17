<div class="py-12">
    <h2 class="text-center text-2xl font-bold text-white mb-2">Nasıl hissediyorsun?</h2>
    <p class="text-center text-gray-400 mb-8">Ruh haline göre sana özel film önerileri seçelim</p>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4 max-w-6xl mx-auto px-4">
        @foreach($moods as $slug => $mood)
            @php
                $isAdult = !empty($mood['adult']);
                $locked = $isAdult && (!Auth::check() || !Auth::user()->isAdult());
            @endphp
            <button
                wire:click="{{ $locked ? '' : "selectMood('{$slug}')" }}"
                class="relative group rounded-2xl p-5 text-center transition-all duration-300 cursor-pointer
                       bg-gradient-to-br {{ $mood['gradient'] }}
                       {{ $selectedMood === $slug ? 'ring-4 ring-white scale-105' : 'opacity-90 hover:opacity-100' }}
                       {{ $locked ? 'cursor-not-allowed opacity-50 hover:opacity-50' : 'hover:scale-105' }}"
                @if($locked)
                   onclick="window.location='{{ url('/giris') }}'"
                @endif
            >
                <span class="text-4xl block mb-2">
                    {{ $mood['emoji'] }}
                </span>
                <span class="text-white font-semibold text-sm block">
                    {{ $mood['label'] }}
                    @if($locked)
                        🔒
                    @endif
                </span>
                <span class="text-white/70 text-xs block mt-1">{{ $mood['description'] }}</span>
                @if($locked)
                    <span class="text-white/70 text-[10px] block mt-1">Giriş yap & 18+</span>
                @endif
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
