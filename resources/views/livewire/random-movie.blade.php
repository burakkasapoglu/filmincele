<div class="text-center py-4">
    @if(!$showPicker)
        <button wire:click="togglePicker"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-rose-600 to-purple-600 hover:from-rose-500 hover:to-purple-500 text-white font-bold text-lg rounded-2xl transition-all duration-300 shadow-lg shadow-rose-600/20 hover:shadow-rose-600/40 hover:scale-105 disabled:opacity-50 disabled:cursor-wait">
            🎲 Ne İzlesem?
        </button>
    @else
        <div class="inline-flex items-center gap-3 bg-gray-900 border border-gray-800 rounded-2xl p-2">
            <button wire:click="pickMovie"
                    wire:loading.attr="disabled"
                    class="px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-200
                           bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white
                           shadow-lg shadow-rose-600/20 disabled:opacity-50">
                🎬 Film Öner
            </button>
            <span class="text-gray-400 text-xs">veya</span>
            <button wire:click="pickTV"
                    wire:loading.attr="disabled"
                    class="px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-200
                           bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white
                           shadow-lg shadow-purple-600/20 disabled:opacity-50">
                📺 Dizi Öner
            </button>
        </div>
    @endif
    <p class="text-gray-500 text-sm mt-3">Kararsız kaldığında senin için rastgele seçelim</p>
</div>
