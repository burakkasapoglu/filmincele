<div>
    @auth
        @if(Auth::id() !== $user->id)
            <button wire:click="toggle"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $isFollowing ? 'bg-gray-800 text-gray-300 hover:bg-red-600/20 hover:text-red-400' : 'bg-rose-600 hover:bg-rose-500 text-white' }}">
                {{ $isFollowing ? 'Takipten Çık' : 'Takip Et' }}
            </button>
        @endif
    @endauth
</div>
