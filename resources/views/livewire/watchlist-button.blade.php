<div class="relative">
    @if($feedback)
        <div class="mb-2 px-3 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-emerald-400 text-xs flex items-center justify-between"
             wire:poll.3s="$set('feedback', null)">
            <span>✓ {{ $feedback }}</span>
            <button wire:click="$set('feedback', null)" class="text-emerald-400 hover:text-emerald-300">✕</button>
        </div>
    @endif

    @if($isInWatchlist)
        <div class="flex items-center gap-2 mb-2">
            <span class="text-green-400 text-sm">✓ Listende</span>
            <button wire:click="toggleDropdown"
                    class="text-xs text-gray-400 hover:text-white transition underline">
                Düzenle
            </button>
        </div>
    @endif

    <button wire:click="toggleDropdown"
            class="w-full px-4 py-2.5 {{ $isInWatchlist ? 'bg-green-600 hover:bg-green-500' : 'bg-rose-600 hover:bg-rose-500' }} text-white text-sm font-medium rounded-xl transition duration-200">
        @if($isInWatchlist)
            ✓ Listeme Eklendi
        @else
            + Listeme Ekle
        @endif
    </button>

    @if($showDropdown)
        <div class="fixed inset-0 z-30" wire:click="toggleDropdown"></div>
        <div class="absolute top-full left-0 right-0 mt-2 bg-gray-800 border border-gray-700 rounded-xl shadow-xl z-40 overflow-hidden">
            <div class="p-2 max-h-60 overflow-y-auto">
                <p class="text-gray-500 text-xs px-3 py-2 uppercase tracking-wide">Listeye ekle</p>

                @foreach($userWatchlists as $list)
                    @php
                        $mId = \App\Models\Movie::where('tmdb_id', $this->tmdbId)->first()?->id;
                        $inList = $mId && \App\Models\Watchlist::find($list['id'])
                            ?->movies()->where('movie_id', $mId)->exists();
                    @endphp
                    <button wire:click="toggleList({{ $list['id'] }})"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition hover:bg-gray-700 flex items-center justify-between">
                        <span class="text-white">{{ $list['name'] }}</span>
                        <span class="text-gray-500 text-xs">{{ $list['movies_count'] ?? 0 }} film</span>
                        @if($inList)
                            <span class="text-green-400 text-xs ml-2">✓</span>
                        @endif
                    </button>
                @endforeach

                <div class="border-t border-gray-700 my-1"></div>

                @if($showNewListInput)
                    <div class="px-3 py-2 space-y-2">
                        <input type="text" wire:model="newListName" placeholder="Liste adı..."
                               class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm placeholder-gray-500 focus:ring-rose-500 focus:border-rose-500"
                               wire:keydown.enter="createAndAdd" autofocus>
                        <div class="flex gap-2">
                            <button wire:click="createAndAdd"
                                    class="flex-1 px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs rounded-lg transition">
                                Oluştur ve Ekle
                            </button>
                            <button wire:click="$set('showNewListInput', false)"
                                    class="px-3 py-1.5 text-gray-400 hover:text-white text-xs transition">
                                İptal
                            </button>
                        </div>
                    </div>
                @else
                    <button wire:click="showNewList"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm text-rose-400 hover:bg-gray-700 transition">
                        + Yeni Liste Oluştur
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
