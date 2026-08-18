@extends('admin.layout')
@section('admin-content')
<div>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">💡 İçerik Fikirleri</h1>
            <p class="text-gray-500 text-sm mt-1">Öneriler, çekim planlaması ve yayın takibi</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-5 border-b border-gray-800/50 pb-px">
        @php
            $tabs = [
                'suggestions' => '💡 Öneriler',
                'planned' => '📅 Planlananlar (' . $counts['planned'] . ')',
                'published' => '✅ Paylaşılanlar (' . $counts['published'] . ')',
            ];
        @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.ideas', ['tab' => $key]) }}"
               class="px-4 py-2.5 text-sm rounded-t-xl transition {{ $tab === $key
                   ? 'bg-gray-900 text-white font-medium border-t border-x border-gray-800/50'
                   : 'text-gray-400 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">✓ {{ session('success') }}</div>
    @endif
    @if(session('idea_error'))
        <div class="mb-4 px-4 py-3 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-sm">✗ {{ session('idea_error') }}</div>
    @endif

    <div class="space-y-3">
        @forelse($ideas as $idea)
            @php
                $statusColors = [
                    'new' => 'bg-gray-800 text-gray-400',
                    'planned' => 'bg-amber-600/10 text-amber-400 border border-amber-600/20',
                    'published' => 'bg-emerald-600/10 text-emerald-400 border border-emerald-600/20',
                    'dismissed' => 'bg-gray-900/50 text-gray-600',
                ];
                $statusLabels = ['new' => 'Yeni', 'planned' => 'Planlandı', 'published' => 'Paylaşıldı', 'dismissed' => 'Geçildi'];
                $videoScript = !empty($idea['script']) ? json_decode($idea['script'], true) : null;
                $isDue = !empty($idea['event_date']) && $idea['event_date'] <= now()->toDateString() && $idea['status'] === 'planned';
            @endphp
            <div class="bg-gray-900 rounded-2xl border {{ $isDue ? 'border-amber-500/40 ring-1 ring-amber-500/20' : 'border-gray-800/50' }} p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="text-lg">{{ $idea['icon'] }}</span>
                            <h3 class="text-white font-medium">{{ $idea['title'] }}</h3>
                            <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-md {{ $statusColors[$idea['status']] ?? '' }}">{{ $statusLabels[$idea['status']] ?? $idea['status'] }}</span>
                            @if($idea['when_label'] ?? null)
                                <span class="{{ $isDue ? 'text-amber-400 font-semibold' : 'text-rose-400/80' }} text-xs">📅 {{ $isDue ? 'ÇEKİM GÜNÜ — ' : '' }}{{ $idea['when_label'] }}</span>
                            @endif
                            @if($videoScript)
                                <span class="text-[10px] bg-rose-600/10 text-rose-400 px-2 py-0.5 rounded-md border border-rose-600/20">🎬 Video metni hazır</span>
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
                        <input type="hidden" name="type" value="{{ $idea['type'] }}">
                        <input type="hidden" name="tmdb_ref" value="{{ $idea['tmdb_ref'] }}">
                        <input type="hidden" name="suggestion" value="{{ $idea['suggestion'] ?? '' }}">
                        <input type="hidden" name="event_date" value="{{ $idea['event_date'] ?? '' }}">
                        <button class="px-3 py-1.5 text-xs bg-rose-600 hover:bg-rose-500 text-white rounded-lg transition">
                            {{ $videoScript ? '🔄 Video Metnini Yenile' : '🎬 Video Metni Üret' }}
                        </button>
                    </form>

                    @if($tab === 'suggestions')
                        <form method="POST" action="{{ route('admin.ideas.blog') }}" class="inline">
                            @csrf
                            <input type="hidden" name="ai_topic" value="{{ $idea['title'] }}. {{ $idea['suggestion'] ?? '' }}">
                            <button class="px-3 py-1.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition">📝 Blog Yazısı Üret</button>
                        </form>
                    @endif

                    @foreach(['planned' => '📅 Planla', 'published' => '✓ Paylaşıldı', 'dismissed' => '→ Geç'] as $status => $label)
                        @if(($idea['status'] ?? 'new') !== $status)
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

                @if($videoScript)
                    @php $openByDefault = $tab !== 'suggestions'; @endphp
                    <details class="mt-4 group" {{ $openByDefault ? 'open' : '' }}>
                        <summary class="cursor-pointer text-xs text-rose-400 hover:text-rose-300 select-none">
                            🎬 Video metni hazır — {{ $videoScript['video_title'] ?? '' }} <span class="text-gray-500">(aç/kapa)</span>
                        </summary>
                        <div class="mt-3 bg-gray-950/60 border border-gray-800/50 rounded-xl p-5 space-y-4">
                            @if(!empty($videoScript['hook']))
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-amber-400 mb-1">⚡ Hook (ilk 3 saniye)</p>
                                    <p class="text-white text-sm font-medium">{{ $videoScript['hook'] }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">🗣️ Konuşma Metni</p>
                                <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">{{ $videoScript['script'] }}</p>
                            </div>
                            @if(!empty($videoScript['visual_notes']))
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-1">🎥 Çekim Planı</p>
                                    <ul class="text-gray-400 text-xs space-y-1 list-disc list-inside">
                                        @foreach($videoScript['visual_notes'] as $note)
                                            <li>{{ $note }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!empty($videoScript['sm_caption']) || !empty($videoScript['hashtags']))
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-cyan-400 mb-1">📱 Sosyal Medya Paylaşımı</p>
                                    @if(!empty($videoScript['sm_caption']))
                                        <p class="text-gray-300 text-sm">{{ $videoScript['sm_caption'] }}</p>
                                    @endif
                                    @if(!empty($videoScript['hashtags']))
                                        <p class="text-blue-400/80 text-xs mt-2 leading-relaxed">{{ implode(' ', $videoScript['hashtags']) }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </details>
                @endif
            </div>
        @empty
            <div class="bg-gray-900 rounded-2xl border border-gray-800/50 p-10 text-center text-gray-500">
                @if($tab === 'planned')
                    Henüz planlanmış fikir yok. Öneriler sekmesinden 📅 Planla veya 🎬 Video Metni Üret ile planlamaya ekleyin.
                @elseif($tab === 'published')
                    Henüz paylaşılmış içerik yok. Çekimini yaptıktan sonra fikri ✓ Paylaşıldı işaretleyin.
                @else
                    Şu an için öneri üretilemedi.
                @endif
            </div>
        @endforelse
    </div>
</div>
@endsection
