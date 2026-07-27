@extends('admin.layout')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<style>
    .EasyMDEContainer { border-radius: 0.75rem; overflow: hidden; margin-top: 0; }
    .EasyMDEContainer .editor-toolbar { background: rgb(31 41 55); border-color: rgb(55 65 81); border-radius: 0.75rem 0.75rem 0 0; padding: 0 0.5rem; }
    .EasyMDEContainer .editor-toolbar button { color: rgb(209 213 219) !important; }
    .EasyMDEContainer .editor-toolbar button:hover { background: rgb(55 65 81) !important; border-color: rgb(75 85 99) !important; }
    .EasyMDEContainer .editor-toolbar button.active { background: rgb(55 65 81) !important; border-color: rgb(244 63 94) !important; }
    .EasyMDEContainer .CodeMirror { background: rgb(31 41 55); border-color: rgb(55 65 81); color: #fff; font-size: 14px; line-height: 1.7; border-radius: 0 0 0.75rem 0.75rem; min-height: 500px; }
    .CodeMirror .cm-header { color: #f472b6 !important; }
    .CodeMirror .cm-link { color: #60a5fa !important; }
    .CodeMirror .cm-string { color: #a78bfa !important; }
    .CodeMirror .cm-strong { color: #fbbf24 !important; font-weight: bold; }
    .editor-preview, .editor-preview-side { background: rgb(17 24 39) !important; color: #d1d5db !important; padding: 1.25rem !important; }
    .editor-preview a { color: #f472b6 !important; }
    .editor-preview h1, .editor-preview h2, .editor-preview h3 { color: #fff !important; }
    .editor-statusbar { background: rgb(31 41 55) !important; border-color: rgb(55 65 81) !important; color: #6b7280 !important; }
    .editor-preview-active-side { border-color: rgb(55 65 81) !important; }
</style>
@endpush

@section('admin-content')
<div class="max-w-full">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.posts') }}" class="text-gray-500 hover:text-white transition text-sm">← Blog</a>
        <span class="text-gray-700">/</span>
        <h1 class="text-xl font-bold text-white">{{ $post ? 'Yazıyı Düzenle' : 'Yeni Yazı' }}</h1>
    </div>

    {{-- AI Generation Panel --}}
    @if(!$post)
    <div class="bg-gradient-to-r from-violet-900/30 to-purple-900/30 border border-violet-600/20 rounded-2xl p-5 mb-6">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xl">🤖</span>
            <h3 class="text-white font-semibold text-sm">Yapay Zeka ile Blog Yazısı Oluştur</h3>
        </div>
        <div class="flex gap-3">
            <input type="text" id="ai-topic" placeholder="Konu yaz... (örn: En iyi korku filmleri 2026)"
                   class="flex-1 bg-gray-800 border border-gray-700 rounded-xl px-4 py-2.5 text-white text-sm placeholder-gray-500 focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition">
            <button type="button" onclick="generateAI()" id="ai-btn"
                    class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-xl transition disabled:opacity-50">
                ✨ Oluştur
            </button>
        </div>
        <p id="ai-status" class="text-gray-500 text-xs mt-2 hidden"></p>
    </div>
    @endif

    <form method="POST" action="{{ $post ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data">
        @csrf
        @if($post) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Main Editor --}}
            <div class="lg:col-span-3 space-y-5">
                <div class="bg-gray-900 rounded-xl border border-gray-800 p-5">
                    <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-lg font-semibold placeholder-gray-500 focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition mb-4"
                           placeholder="Yazı başlığı...">

                    <textarea name="excerpt" rows="2"
                              class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition mb-4"
                              placeholder="Kısa bir özet...">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>

                    <textarea id="editor" name="body" required>{{ old('body', $post->body ?? "## Başlık\n\nYazı içeriğinizi buraya yazın...\n\n**[kalın metin](bağlantı)**\n\n1. Liste öğesi\n2. Liste öğesi") }}</textarea>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">
                <div class="bg-gray-900 rounded-xl border border-gray-800 p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Kategori</label>
                        <select name="category" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition">
                            @foreach(['Rehber', 'Liste', 'Haber', 'Analiz', 'Eğlence', 'Tartışma', 'Tür Analizi', 'Tarih', 'Teknoloji', 'Festival', 'Profil', 'Trend'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $post->category ?? 'Liste') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Okuma Süresi (dk)</label>
                        <input type="number" name="read_time" value="{{ old('read_time', $post->read_time ?? 5) }}" min="1"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-rose-500/50 focus:border-rose-500 transition">
                    </div>
                    <div class="border-t border-gray-800 pt-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published ?? true) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-lg bg-gray-800 border-gray-700 text-rose-600 focus:ring-rose-500 transition">
                            <span class="text-sm text-gray-300">Yayınla</span>
                        </label>
                    </div>
                </div>

                {{-- Cover Image --}}
                <div class="bg-gray-900 rounded-xl border border-gray-800 p-5 space-y-4">
                    <h3 class="text-sm font-medium text-white">Kapak Görseli</h3>
                    @php $imgUrl = old('image_url', $post->image_url ?? ''); @endphp
                    <div id="image-preview" class="aspect-video rounded-xl bg-gray-800 overflow-hidden {{ $imgUrl ? '' : 'hidden' }}">
                        <img src="{{ $imgUrl }}" class="w-full h-full object-cover" id="preview-img">
                    </div>
                    <input type="url" name="image_url" id="image-url" value="{{ $imgUrl }}"
                           placeholder="https://image.tmdb.org/..."
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-sm placeholder-gray-500"
                           oninput="document.getElementById('preview-img').src=this.value;document.getElementById('image-preview').classList.toggle('hidden',!this.value)">
                    <div class="border-t border-gray-800 pt-4">
                        <input type="file" name="image_file" id="image-file" accept="image/*"
                               class="w-full text-sm text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-gray-700 file:text-white hover:file:bg-gray-600 transition"
                               onchange="handleFileUpload(this)">
                    </div>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-rose-600 hover:bg-rose-500 text-white font-medium rounded-xl transition">
                    {{ $post ? 'Güncelle' : 'Yayınla' }}
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<script>
(function() {
    if (typeof EasyMDE === 'undefined') { console.error('EasyMDE yüklenemedi'); return; }
    const el = document.getElementById('editor');
    if (!el) { console.error('Editor textarea bulunamadı'); return; }
    const easyMDE = new EasyMDE({
        element: el,
        spellChecker: false,
        placeholder: 'Yazınızı buraya yazın...',
        autosave: { enabled: true, uniqueId: 'blog-editor', delay: 3000 },
        toolbar: ['bold', 'italic', 'heading-2', 'heading-3', '|',
                  'link', 'image', '|', 'unordered-list', 'ordered-list', '|',
                  'preview', 'side-by-side', 'fullscreen', '|', 'guide'],
        status: ['lines', 'words'],
        renderingConfig: { codeSyntaxHighlighting: true },
        uploadImage: true,
        imageUploadEndpoint: '{{ url("/admin/blog/image-upload") }}',
        imageCSRFToken: '{{ csrf_token() }}',
        imagePathAbsolute: true,
    });
    window.easyMDE = easyMDE;
})();

function handleFileUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('image-url').value = '';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function generateAI() {
    const topic = document.getElementById('ai-topic').value.trim();
    if (!topic) return alert('Lütfen bir konu yazın.');

    const btn = document.getElementById('ai-btn');
    const status = document.getElementById('ai-status');
    btn.disabled = true;
    btn.textContent = '⏳ Oluşturuluyor...';
    status.classList.remove('hidden');
    status.textContent = 'Yapay zeka yazıyor...';

    try {
        const res = await fetch('{{ route("admin.posts.ai-generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ topic })
        });

        const data = await res.json();

        if (data.error) {
            status.textContent = '❌ ' + data.error;
            status.classList.remove('text-gray-500');
            status.classList.add('text-red-400');
        } else {
            document.querySelector('input[name="title"]').value = data.title;
            document.querySelector('textarea[name="excerpt"]').value = data.excerpt || '';

            if (window.easyMDE) {
                window.easyMDE.value(data.body);
            }

            if (data.image_url) {
                document.getElementById('image-url').value = data.image_url;
                document.getElementById('preview-img').src = data.image_url;
                document.getElementById('image-preview').classList.remove('hidden');
            }

            const catSelect = document.querySelector('select[name="category"]');
            if (catSelect && data.category) {
                for (let opt of catSelect.options) {
                    if (opt.value === data.category) { opt.selected = true; break; }
                }
            }

            status.textContent = '✅ İçerik oluşturuldu! Düzenleyip yayınlayabilirsiniz.';
            status.classList.remove('text-red-400');
            status.classList.add('text-emerald-400');
        }
    } catch (e) {
        status.textContent = '❌ Bağlantı hatası.';
        status.classList.add('text-red-400');
    }

    btn.disabled = false;
    btn.textContent = '✨ Oluştur';
}
</script>
@endsection
