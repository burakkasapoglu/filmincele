@extends('admin.layout')
@section('admin-content')
<div>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">💡 İçerik Fikirleri</h1>
            <p class="text-gray-500 text-sm mt-1">Önümüzdeki 3 hafta: doğum günleri, yıl dönümleri, trendler ve içerik fikirleri</p>
        </div>
        <div class="flex gap-2 text-xs">
            <span class="px-3 py-1.5 bg-gray-800 rounded-lg text-gray-400">Yeni: <b class="text-white">{{ $stats['new'] }}</b></span>
            <span class="px-3 py-1.5 bg-amber-600/10 border border-amber-600/20 rounded-lg text-amber-400">Planlandı: <b>{{ $stats['planned'] }}</b></span>
            <span class="px-3 py-1.5 bg-emerald-600/10 border border-emerald-600/20 rounded-lg text-emerald-400">Paylaşıldı: <b>{{ $stats['published'] }}</b></span>
            <span class="px-3 py-1.5 bg-gray-800/50 rounded-lg text-gray-500">Geçildi: <b>{{ $stats['dismissed'] }}</b></span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">✓ {{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @foreach($ideas as $idea)
            @php
                $statusColors = [
                    'new' => 'bg-gray-800 text-gray-400',
                    'planned' => 'bg-amber-600/10 text-amber-400 border border-amber-600/20',
                    'published' => 'bg-emerald-600/10 text-emerald-400 border border-emerald-600/20',
                    'dismissed' => 'bg-gray-900/50 text-gray-600',
                ];
                $statusLabels = ['new' => 'Yeni', 'planned' => 'Planlandı', 'published' => 'Paylaşıldı', 'dismissed' => 'Geçildi'];
            @endphp
            <div class="bg-gray-900 rounded-2xl border {{ $idea['status'] === 'dismissed' ? 'border-gray-800/30 opacity-60' : 'border-gray-800/50' }} p-5 {{ $idea['status'] === 'planned' ? 'ring-1 ring-amber-600/20' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="text-lg">{{ $idea['icon'] }}</span>
                            <h3 class="text-white font-medium">{{ $idea['title'] }}</h3>
                            <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-md {{ $statusColors[$idea['status']] }}">{{ $statusLabels[$idea['status']] }}</span>
                            @if($idea['when_label'] ?? null)
                                <span class="text-rose-400/80 text-xs">📅 {{ $idea['when_label'] }}</span>
                            @endif
                        </div>
                        @if($idea['suggestion'] ?? null)
                            <p class="text-gray-400 text-sm leading-relaxed max-w-3xl">{{ $idea['suggestion'] }}</p>
                        @endif
                        @if($idea['post'] ?? null)
                            <a href="{{ url('/blog/' . $idea['post']->slug) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-400 text-xs mt-2 hover:underline">
                                ✓ Yayınlandı: {{ \Illuminate\Support\Str::limit($idea['post']->title, 60) }} <span>↗</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mt-4">
                    <form method="POST" action="{{ route('admin.ideas.generate') }}" class="inline">
                        @csrf
                        <input type="hidden" name="title" value="{{ $idea['title'] }}">
                        <input type="hidden" name="suggestion" value="{{ $idea['suggestion'] ?? '' }}">
                        <button class="px-3 py-1.5 text-xs bg-rose-600 hover:bg-rose-500 text-white rounded-lg transition">✨ AI ile Yaz</button>
                    </form>

                    @foreach(['planned' => '📅 Planla', 'published' => '✓ Paylaşıldı', 'dismissed' => '→ Geç'] as $status => $label)
                        @if($idea['status'] !== $status)
                            <form method="POST" action="{{ route('admin.ideas.status') }}" class="inline">
                                @csrf
                                <input type="hidden" name="title" value="{{ $idea['title'] }}">
                                <input type="hidden" name="type" value="{{ $idea['type'] }}">
                                <input type="hidden" name="tmdb_ref" value="{{ $idea['tmdb_ref'] }}">
                                <input type="hidden" name="suggestion" value="{{ $idea['suggestion'] ?? '' }}">
                                <input type="hidden" name="event_date" value="{{ $idea['event_date'] ?? '' }}">
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button class="px-3 py-1.5 text-xs {{ $status === 'dismissed' ? 'text-gray-500 hover:text-gray-300 bg-gray-800/50' : 'text-gray-300 hover:text-white bg-gray-800' }} rounded-lg transition">{{ $label }}</button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        @if($ideas->isEmpty())
            <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-10 text-center text-gray-500">
                Şu an için öneri üretilemedi.
            </div>
        @endif
    </div>
</div>
@endsection
