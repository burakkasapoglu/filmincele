<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">📺 Platforma Göre Keşfet</h1>
        <p class="text-gray-400">Türkiye'deki tüm platformlar — hangisinde ne izleyeceğine birlikte karar verelim</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($platforms as $p)
            <a href="{{ url('/platform/' . $p['id'] . '/' . \Illuminate\Support\Str::slug($p['name'])) }}"
               class="cinema-card group relative rounded-2xl overflow-hidden p-6 text-center"
               style="background: radial-gradient(120% 120% at 50% 0%, {{ $p['color'] }}22 0%, rgb(17 24 39) 60%); border: 1px solid {{ $p['color'] }}33">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl flex items-center justify-center text-3xl"
                     style="background: {{ $p['color'] }}22; border: 1px solid {{ $p['color'] }}40">
                    {{ $p['emoji'] }}
                </div>
                <h3 class="text-white font-semibold text-sm group-hover:text-amber-400 transition">{{ $p['name'] }}</h3>
                <p class="text-gray-500 text-xs mt-1">{{ $counts[$p['id']] ?? 0 }}+ içerik</p>
                <span class="absolute top-3 right-3 w-8 h-8 rounded-full bg-gray-900/70 flex items-center justify-center text-gray-400 opacity-0 group-hover:opacity-100 transition">→</span>
            </a>
        @endforeach
    </div>
</div>