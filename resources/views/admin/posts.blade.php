@extends('admin.layout')
@section('admin-content')
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Blog Yazıları</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $posts->total() }} yazı</p>
        </div>
        <a href="{{ route('admin.posts.create') }}"
           class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-lg shadow-rose-600/20 inline-flex items-center gap-2">
            <span>+</span> Yeni Yazı
        </a>
    </div>

    <div class="bg-gray-900 rounded-2xl border border-gray-800/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800/50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-4 font-medium">Yazı</th>
                        <th class="text-left px-5 py-4 font-medium hidden md:table-cell">Kategori</th>
                        <th class="text-center px-5 py-4 font-medium hidden md:table-cell">Durum</th>
                        <th class="text-center px-5 py-4 font-medium">Tarih</th>
                        <th class="text-center px-5 py-4 font-medium">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach($posts as $post)
                        <tr class="hover:bg-gray-800/30 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($post->image_url)
                                        <div class="w-12 h-8 rounded-lg overflow-hidden bg-gray-800 flex-shrink-0 hidden sm:block">
                                            <img src="{{ $post->image_url }}" class="w-full h-full object-cover" alt="{{ $post->title }}">
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-white truncate max-w-xs">{{ $post->title }}</p>
                                        <p class="text-gray-500 text-xs mt-0.5 line-clamp-1 max-w-xs">
                                            {{ $post->read_time }} dk · {{ $post->excerpt ? \Illuminate\Support\Str::limit($post->excerpt, 70) : '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <span class="text-gray-400 text-xs">{{ $post->category }}</span>
                            </td>
                            <td class="px-5 py-4 text-center hidden md:table-cell">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $post->is_published ? 'bg-emerald-600/10 text-emerald-400 border border-emerald-600/20' : 'bg-gray-800 text-gray-500' }}">
                                    {{ $post->is_published ? 'Yayında' : 'Taslak' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center text-gray-400 text-xs">
                                {{ $post->published_at->format('d.m.Y') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-rose-400 hover:text-rose-300 text-xs font-medium transition">Düzenle</a>
                                    <form method="POST" action="{{ route('admin.posts.share', $post) }}" class="inline">
                                        @csrf
                                        <button class="text-blue-400 hover:text-blue-300 text-xs transition">Paylaş</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.posts.delete', $post) }}" onsubmit="return confirm('Bu yazıyı silmek istediğine emin misin?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-500 hover:text-red-400 text-xs transition">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>
</div>
@endsection
