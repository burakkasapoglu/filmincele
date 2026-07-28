<div class="max-w-2xl mx-auto space-y-6">
    @if($successMessage)
        <div class="px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">{{ $successMessage }}</div>
    @endif

    {{-- Profile Photo --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-6">
        <h2 class="text-white font-semibold mb-4">Profil Fotoğrafı</h2>
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-800 flex-shrink-0">
                <x-avatar size="w-20 h-20" />
            </div>
            <div>
                <input type="file" wire:model="photo" accept="image/*" class="text-sm text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-gray-700 file:text-white hover:file:bg-gray-600">
                @error('photo') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                @if($photo)
                    <button wire:click="updatePhoto" class="mt-2 px-4 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-sm rounded-lg transition">Fotoğrafı Yükle</button>
                @endif
                <div wire:loading wire:target="photo" class="text-gray-400 text-xs mt-1">Yükleniyor...</div>
            </div>
        </div>
    </div>

    {{-- Basic Info --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-6 space-y-4">
        <h2 class="text-white font-semibold">Temel Bilgiler</h2>

        <div>
            <label class="block text-sm text-gray-300 mb-1">Ad Soyad</label>
            <input type="text" wire:model="name"
                   class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:ring-rose-500 focus:border-rose-500">
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-1">E-posta</label>
            <input type="email" value="{{ $email }}" disabled
                   class="w-full bg-gray-800/50 border border-gray-700/50 rounded-xl px-4 py-2.5 text-gray-500 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-1">Doğum Tarihi</label>
            <input type="date" wire:model="birthDate" max="{{ now()->subYears(13)->format('Y-m-d') }}"
                   class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:ring-rose-500 focus:border-rose-500">
        </div>

        <button wire:click="updateProfile" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-medium rounded-xl transition">Kaydet</button>
    </div>

    {{-- About --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-6 space-y-4">
        <h2 class="text-white font-semibold">Hakkında</h2>

        <div>
            <label class="block text-sm text-gray-300 mb-1">Biyografi</label>
            <textarea wire:model="bio" rows="3" placeholder="Kendinden kısaca bahset..."
                      class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:ring-rose-500 focus:border-rose-500"></textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-1">Konum</label>
            <input type="text" wire:model="location" placeholder="İstanbul, Türkiye"
                   class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:ring-rose-500 focus:border-rose-500">
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-1">Web Sitesi</label>
            <input type="url" wire:model="website" placeholder="https://..."
                   class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:ring-rose-500 focus:border-rose-500">
        </div>

        <button wire:click="updateProfile" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-medium rounded-xl transition">Kaydet</button>
    </div>

    {{-- Password --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-6">
        @if(!$showPasswordForm)
            <button wire:click="$set('showPasswordForm', true)" class="text-sm text-gray-400 hover:text-white transition">Şifre Değiştir</button>
        @else
            <h2 class="text-white font-semibold mb-4">Şifre Değiştir</h2>
            @if($errorMessage)
                <div class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">{{ $errorMessage }}</div>
            @endif
            <div class="space-y-3">
                <input type="password" wire:model="currentPassword" placeholder="Mevcut şifre" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500">
                <input type="password" wire:model="newPassword" placeholder="Yeni şifre" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500">
                <input type="password" wire:model="newPasswordConfirmation" placeholder="Yeni şifre tekrar" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500">
                <div class="flex gap-3">
                    <button wire:click="updatePassword" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-sm transition">Değiştir</button>
                    <button wire:click="$set('showPasswordForm', false)" class="px-4 py-2 text-gray-400 hover:text-white text-sm transition">İptal</button>
                </div>
            </div>
        @endif
    </div>
</div>
