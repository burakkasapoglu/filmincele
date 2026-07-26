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

    {{-- First Row: Site Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-600/10 to-blue-600/5 border border-blue-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">👥</span><span class="text-blue-400 text-xs font-medium bg-blue-600/10 px-2 py-0.5 rounded-lg">Toplam</span></div>
            <p class="text-3xl font-bold text-white">{{ $userCount }}</p>
            <p class="text-gray-500 text-xs mt-1">Kayıtlı Üye</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-600/10 to-yellow-600/5 border border-yellow-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">⭐</span><span class="text-yellow-400 text-xs font-medium bg-yellow-600/10 px-2 py-0.5 rounded-lg">Toplam</span></div>
            <p class="text-3xl font-bold text-yellow-400">{{ $ratingCount }}</p>
            <p class="text-gray-500 text-xs mt-1">Puanlama</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-600/10 to-emerald-600/5 border border-emerald-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">📋</span><span class="text-emerald-400 text-xs font-medium bg-emerald-600/10 px-2 py-0.5 rounded-lg">Toplam</span></div>
            <p class="text-3xl font-bold text-white">{{ $watchlistCount }}</p>
            <p class="text-gray-500 text-xs mt-1">Liste</p>
        </div>
        <div class="bg-gradient-to-br from-rose-600/10 to-rose-600/5 border border-rose-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">📝</span><span class="text-rose-400 text-xs font-medium bg-rose-600/10 px-2 py-0.5 rounded-lg">Toplam</span></div>
            <p class="text-3xl font-bold text-white">{{ $postCount }}</p>
            <p class="text-gray-500 text-xs mt-1">Blog</p>
        </div>
    </div>

    {{-- Second Row: Traffic Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-violet-600/10 to-violet-600/5 border border-violet-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">👁️</span><span class="text-violet-400 text-xs font-medium bg-violet-600/10 px-2 py-0.5 rounded-lg">Bugün</span></div>
            <p class="text-3xl font-bold text-white">{{ $todayViews }}</p>
            <p class="text-gray-500 text-xs mt-1">Sayfa Görüntüleme</p>
        </div>
        <div class="bg-gradient-to-br from-cyan-600/10 to-cyan-600/5 border border-cyan-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">📈</span><span class="text-cyan-400 text-xs font-medium bg-cyan-600/10 px-2 py-0.5 rounded-lg">7 Gün</span></div>
            <p class="text-3xl font-bold text-white">{{ $weekViews }}</p>
            <p class="text-gray-500 text-xs mt-1">Sayfa Görüntüleme</p>
        </div>
        <div class="bg-gradient-to-br from-amber-600/10 to-amber-600/5 border border-amber-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">🌐</span><span class="text-amber-400 text-xs font-medium bg-amber-600/10 px-2 py-0.5 rounded-lg">Toplam</span></div>
            <p class="text-3xl font-bold text-white">{{ $totalViews }}</p>
            <p class="text-gray-500 text-xs mt-1">Toplam Görüntüleme</p>
        </div>
        <div class="bg-gradient-to-br from-pink-600/10 to-pink-600/5 border border-pink-600/20 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3"><span class="text-2xl">👤</span><span class="text-pink-400 text-xs font-medium bg-pink-600/10 px-2 py-0.5 rounded-lg">Bugün</span></div>
            <p class="text-3xl font-bold text-white">{{ $uniqueVisitors }}</p>
            <p class="text-gray-500 text-xs mt-1">Tekil Ziyaretçi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Movies --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-4">En Çok Ziyaret Edilen Filmler</h2>
            @if($topMovies->count())
                <div class="space-y-2">
                    @foreach($topMovies as $item)
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-gray-500 w-6 text-right">{{ $loop->iteration }}.</span>
                            <span class="text-white flex-1 truncate">{{ $item->movie_id }}</span>
                            <span class="text-violet-400 text-xs">{{ $item->count }} görüntülenme</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm py-4 text-center">Henüz veri yok</p>
            @endif
        </div>

        {{-- Top Blog Posts --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-4">En Çok Okunan Blog Yazıları</h2>
            @if($topBlogPosts->count())
                <div class="space-y-2">
                    @foreach($topBlogPosts as $item)
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-gray-500 w-6 text-right">{{ $loop->iteration }}.</span>
                            <span class="text-white flex-1 truncate">{{ $item->url }}</span>
                            <span class="text-violet-400 text-xs">{{ $item->count }} okuma</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm py-4 text-center">Henüz veri yok</p>
            @endif
        </div>
    </div>

    {{-- Daily Views Chart --}}
    <div class="mt-6 bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
        <h2 class="text-white font-semibold mb-4">Son 14 Günlük Ziyaretçi Grafiği</h2>
        @if($dailyViews->count())
            @php $maxVal = $dailyViews->max('count') ?: 1; @endphp
            <div class="flex items-end gap-1 h-32">
                @foreach($dailyViews->reverse() as $day)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-gray-500 text-[10px]">{{ $day->count }}</span>
                        <div class="w-full bg-violet-500/40 rounded-t hover:bg-violet-500/60 transition" style="height: {{ max(($day->count / $maxVal) * 100, 4) }}%"></div>
                        <span class="text-gray-600 text-[10px]">{{ \Carbon\Carbon::parse($day->date)->format('d.m') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm py-8 text-center">Henüz veri yok</p>
        @endif
    </div>
</div>
@endsection
