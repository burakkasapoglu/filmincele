@extends('admin.layout')
@section('admin-content')
<div>
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
            <a href="{{ route('admin.users') }}" class="text-gray-500 hover:text-white text-sm transition inline-flex items-center gap-1 mb-2">← Üyelere Dön</a>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-600/20 to-rose-600/10 flex items-center justify-center text-rose-400 text-xl font-bold">
                    {{ strtoupper(substr($profile->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $profile->name }}</h1>
                    <p class="text-gray-400 text-sm">{{ $profile->email }}</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.users.toggle-admin', $profile) }}">
            @csrf
            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                {{ $profile->is_admin
                    ? 'bg-yellow-600/20 border border-yellow-600/30 text-yellow-400 hover:bg-yellow-600/30'
                    : 'bg-rose-600/20 border border-rose-600/30 text-rose-400 hover:bg-rose-600/30' }}">
                {{ $profile->is_admin ? 'Admin Yetkisini Kaldır' : 'Admin Yap' }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Kayıt Tarihi</p>
            <p class="text-white font-medium">{{ $profile->created_at->format('d.m.Y') }}</p>
            <p class="text-gray-600 text-xs mt-0.5">{{ $profile->created_at->diffForHumans() }}</p>
        </div>
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Puanlamalar</p>
            <p class="text-2xl font-bold text-yellow-400">{{ $profile->ratings_count }}</p>
            <p class="text-gray-500 text-xs mt-0.5">Ortalama: {{ $avgRating }}/10</p>
        </div>
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Listeler</p>
            <p class="text-2xl font-bold text-white">{{ $profile->watchlists_count }}</p>
            <p class="text-gray-500 text-xs mt-0.5">İzleme listesi</p>
        </div>
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Giriş Yöntemi</p>
            <p class="text-white font-medium">{{ $profile->provider ? ucfirst($profile->provider) : 'Email' }}</p>
            <p class="text-gray-600 text-xs mt-0.5">{{ $profile->provider_id ? 'ID: ' . substr($profile->provider_id, 0, 8) . '...' : 'Email/Şifre' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-4">Puanladıkları</h2>
            @if($ratings->count())
                <div class="space-y-1 -mx-4">
                    @foreach($ratings as $rating)
                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-800/30 transition">
                            <div class="w-8 h-8 rounded-lg bg-yellow-600/10 flex items-center justify-center flex-shrink-0">
                                <span class="text-yellow-400 text-xs font-bold">{{ $rating->rating }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-white text-sm">{{ $rating->movie->title ?? 'Film' }}</p>
                                @if($rating->review)
                                    <p class="text-gray-500 text-xs truncate mt-0.5">{{ $rating->review }}</p>
                                @endif
                            </div>
                            <span class="text-gray-600 text-xs flex-shrink-0">{{ $rating->created_at->format('d.m.Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm py-6 text-center">Henüz film puanlamamış.</p>
            @endif
        </div>

        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-4">Favori Türler</h2>
            @if($favoriteGenres->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($favoriteGenres as $genre)
                        <span class="px-3 py-1.5 bg-rose-600/10 border border-rose-600/20 text-rose-400 text-sm rounded-full">{{ $genre->name }}</span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm py-6 text-center">Henüz favori tür seçmemiş.</p>
            @endif
        </div>
    </div>
</div>
@endsection
