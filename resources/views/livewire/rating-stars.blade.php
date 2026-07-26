<div>
    <div class="flex items-center gap-1">
        @for($i = 1; $i <= 10; $i++)
            <button wire:click="setRating({{ $i }})" class="text-2xl transition duration-150 hover:scale-125
                {{ $userRating && $i <= $userRating ? 'text-yellow-400' : 'text-gray-600' }}">
                ★
            </button>
        @endfor
        @if($userRating)
            <span class="text-white font-semibold ml-2">{{ $userRating }}/10</span>
        @endif
    </div>

    @if($showReview)
        <div class="mt-3">
            <textarea wire:model="review"
                      placeholder="Yorumunu yaz..."
                      class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:ring-rose-500 focus:border-rose-500"
                      rows="3"></textarea>
            <button wire:click="saveReview"
                    class="mt-2 px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-sm rounded-lg transition">
                Kaydet
            </button>
        </div>
    @endif
</div>
