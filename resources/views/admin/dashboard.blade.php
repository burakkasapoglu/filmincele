@extends('admin.layout')
@section('admin-content')
<div>
    <div class="flex items-center justify-between mb-8">
        <div><h1 class="text-2xl font-bold text-white">Dashboard</h1><p class="text-gray-500 text-sm mt-1">Hoş geldin, {{ Auth::user()->name }}</p></div>
        <span class="text-gray-600 text-xs">{{ now()->format('d.m.Y') }}</span>
    </div>

    {{-- Site Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-gradient-to-br from-blue-600/10 to-blue-600/5 border border-blue-600/20 rounded-2xl p-4"><p class="text-gray-500 text-xs uppercase tracking-wide">Üye</p><p class="text-2xl font-bold text-white mt-1">{{ $userCount }}</p></div>
        <div class="bg-gradient-to-br from-yellow-600/10 to-yellow-600/5 border border-yellow-600/20 rounded-2xl p-4"><p class="text-gray-500 text-xs uppercase tracking-wide">Puan</p><p class="text-2xl font-bold text-yellow-400 mt-1">{{ $ratingCount }}</p></div>
        <div class="bg-gradient-to-br from-emerald-600/10 to-emerald-600/5 border border-emerald-600/20 rounded-2xl p-4"><p class="text-gray-500 text-xs uppercase tracking-wide">Liste</p><p class="text-2xl font-bold text-white mt-1">{{ $watchlistCount }}</p></div>
        <div class="bg-gradient-to-br from-rose-600/10 to-rose-600/5 border border-rose-600/20 rounded-2xl p-4"><p class="text-gray-500 text-xs uppercase tracking-wide">Blog</p><p class="text-2xl font-bold text-white mt-1">{{ $postCount }}</p></div>
    </div>

    {{-- Traffic Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @php $colors = ['violet','cyan','amber','pink']; $icons = ['👁️','📈','🌐','👤']; $labels = ['Bugün','7 Gün','Toplam','Tekil']; $values = [$todayViews,$weekViews,$totalViews,$uniqueVisitors]; @endphp
        @foreach(range(0,3) as $i)
            <div class="bg-gradient-to-br from-{{$colors[$i]}}-600/10 to-{{$colors[$i]}}-600/5 border border-{{$colors[$i]}}-600/20 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2"><span class="text-lg">{{$icons[$i]}}</span><span class="text-{{$colors[$i]}}-400 text-[10px] font-medium bg-{{$colors[$i]}}-600/10 px-2 py-0.5 rounded-lg">{{$labels[$i]}}</span></div>
                <p class="text-2xl font-bold text-white">{{ number_format($values[$i]) }}</p>
                <p class="text-gray-500 text-[10px] mt-0.5">Sayfa Görüntüleme</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        {{-- Daily Chart --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-4">📊 Son 14 Gün</h2>
            @if($dailyViews->count())
                @php $maxVal = $dailyViews->max('count') ?: 1; @endphp
                <div class="flex items-end gap-[2px] h-36 px-1">
                    @foreach($dailyViews->reverse() as $day)
                        @php $h = max(($day->count / $maxVal) * 100, 3); @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group relative">
                            <span class="text-gray-400 text-[9px] opacity-0 group-hover:opacity-100 transition">{{ $day->count }}</span>
                            <div class="w-full rounded-sm bg-gradient-to-t from-violet-500/60 to-violet-400/30 hover:from-violet-400 hover:to-violet-300/50 transition-all" style="height: {{$h}}%"></div>
                            <span class="text-gray-600 text-[9px]">{{ \Carbon\Carbon::parse($day->date)->format('d.m') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm py-10 text-center">Henüz veri yok</p>
            @endif
        </div>

        {{-- Hourly Chart (Today) --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-4">⏰ Bugün Saatlik</h2>
            @if($hourlyViews->count())
                @php $maxH = $hourlyViews->max('count') ?: 1; $hourlyData = $hourlyViews->pluck('count','hour')->toArray(); @endphp
                <div class="flex items-end gap-[2px] h-36 px-1">
                    @for($h = 0; $h < 24; $h++)
                        @php $c = $hourlyData[$h] ?? 0; $pct = max(($c / $maxH) * 100, 2); @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group relative">
                            <span class="text-gray-400 text-[9px] opacity-0 group-hover:opacity-100 transition">{{ $c }}</span>
                            <div class="w-full rounded-sm bg-gradient-to-t from-cyan-500/60 to-cyan-400/30 hover:from-cyan-400 hover:to-cyan-300/50 transition-all" style="height: {{$pct}}%"></div>
                            <span class="text-gray-600 text-[9px]">{{ $h }}</span>
                        </div>
                    @endfor
                </div>
            @else
                <p class="text-gray-500 text-sm py-10 text-center">Henüz veri yok</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        {{-- Recent Visitors --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-3">👥 Son Ziyaretçiler</h2>
            @if($recentVisitors->count())
                <div class="space-y-1 max-h-80 overflow-y-auto">
                    @foreach($recentVisitors as $v)
                        <div class="flex items-center gap-3 px-3 py-2 hover:bg-gray-800/30 rounded-lg transition text-sm">
                            <div class="w-7 h-7 rounded-full {{ $v->user_id ? 'bg-green-600/20' : 'bg-gray-800' }} flex items-center justify-center flex-shrink-0">
                                <span class="text-[10px] {{ $v->user_id ? 'text-green-400' : 'text-gray-500' }}">{{ $v->user_id ? '👤' : '🕶️' }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-white text-xs truncate">{{ $v->user->name ?? 'Anonim' }}</p>
                                <p class="text-gray-500 text-[10px] truncate">{{ $v->url }}</p>
                            </div>
                            <span class="text-gray-600 text-[10px] flex-shrink-0" title="{{ $v->created_at }}">{{ $v->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm py-8 text-center">Henüz ziyaretçi yok</p>
            @endif
        </div>

        {{-- Top Active Users --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-5">
            <h2 class="text-white font-semibold mb-3">🏆 En Aktif Kullanıcılar (30 gün)</h2>
            @if($topActiveUsers->count())
                <div class="space-y-1">
                    @foreach($topActiveUsers as $u)
                        <div class="flex items-center gap-3 px-3 py-2 hover:bg-gray-800/30 rounded-lg transition">
                            <span class="text-gray-500 text-xs w-5">#{{ $loop->iteration }}</span>
                            <div class="w-7 h-7 rounded-full bg-rose-600/20 flex items-center justify-center text-rose-400 text-[10px] font-semibold">
                                {{ strtoupper(substr($u->user->name ?? '?', 0, 1)) }}
                            </div>
                            <span class="text-white text-xs flex-1">{{ $u->user->name ?? 'Silinmiş' }}</span>
                            <span class="text-violet-400 text-xs font-medium">{{ $u->count }} sayfa</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm py-8 text-center">Henüz veri yok</p>
            @endif
        </div>
    </div>
</div>
@endsection
