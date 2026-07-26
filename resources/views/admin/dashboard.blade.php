@extends('admin.layout')
@section('admin-content')
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Hoş geldin, {{ Auth::user()->name }}</p>
        </div>
        <span class="text-gray-600 text-xs">{{ now()->format('d.m.Y') }}</span>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-600/10 to-blue-600/5 border border-blue-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">👥</span>
                <span class="text-blue-400 text-xs font-medium bg-blue-600/10 px-2 py-0.5 rounded-lg">Toplam</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $userCount }}</p>
            <p class="text-gray-500 text-xs mt-1">Kayıtlı Üye</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-600/10 to-yellow-600/5 border border-yellow-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">⭐</span>
                <span class="text-yellow-400 text-xs font-medium bg-yellow-600/10 px-2 py-0.5 rounded-lg">Toplam</span>
            </div>
            <p class="text-3xl font-bold text-yellow-400">{{ $ratingCount }}</p>
            <p class="text-gray-500 text-xs mt-1">Film Puanlaması</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-600/10 to-emerald-600/5 border border-emerald-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">📋</span>
                <span class="text-emerald-400 text-xs font-medium bg-emerald-600/10 px-2 py-0.5 rounded-lg">Toplam</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $watchlistCount }}</p>
            <p class="text-gray-500 text-xs mt-1">İzleme Listesi</p>
        </div>
        <div class="bg-gradient-to-br from-rose-600/10 to-rose-600/5 border border-rose-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">📝</span>
                <span class="text-rose-400 text-xs font-medium bg-rose-600/10 px-2 py-0.5 rounded-lg">Toplam</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $postCount }}</p>
            <p class="text-gray-500 text-xs mt-1">Blog Yazısı</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Users + Recent Ratings --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-white font-semibold">Son Üyeler</h2>
                    <a href="{{ route('admin.users') }}" class="text-xs text-gray-500 hover:text-rose-400 transition">Tümü →</a>
                </div>
                <div class="space-y-1">
                    @foreach($recentUsers as $user)
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center justify-between hover:bg-gray-800/50 rounded-xl px-4 py-3 -mx-4 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-rose-600/20 to-rose-600/10 flex items-center justify-center text-rose-400 text-xs font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white text-sm group-hover:text-rose-400 transition">{{ $user->name }}</p>
                                    <p class="text-gray-500 text-xs truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-gray-600 text-xs flex-shrink-0">{{ $user->created_at->diffForHumans() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
                <h2 class="text-white font-semibold mb-4">Popüler Film Türleri</h2>
                @if(!empty($popularGenres))
                    <div class="space-y-4">
                        @foreach($popularGenres as $genre)
                            <div class="flex items-center gap-3">
                                <span class="text-white text-sm w-24 flex-shrink-0">{{ $genre->name }}</span>
                                <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-rose-500 to-rose-400 rounded-full transition-all duration-500"
                                         style="width: {{ ($genre->count / max(1, $popularGenres[0]->count)) * 100 }}%"></div>
                                </div>
                                <span class="text-gray-400 text-xs w-16 text-right">{{ $genre->count }} kişi</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Henüz veri yok.</p>
                @endif
            </div>
        </div>

        {{-- Recent Ratings --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-4">Son Puanlamalar</h2>
            <div class="space-y-1 -mx-4">
                @foreach($recentRatings as $rating)
                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-800/50 transition">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-yellow-600/10 flex items-center justify-center">
                            <span class="text-yellow-400 text-xs font-bold">{{ $rating->rating }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-white text-xs truncate">{{ $rating->movie->title ?? 'Film' }}</p>
                            <p class="text-gray-500 text-xs">{{ $rating->user->name ?? '—' }} · {{ $rating->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
