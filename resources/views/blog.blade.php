@extends('layouts.app')
@section('title', 'Blog — Filmincele')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-2">📝 Blog</h1>
    <p class="text-gray-400 mb-8">Film, dizi ve sinema üzerine yazılar</p>

    @php
        $posts = \App\Models\Post::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(12);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
            <a href="{{ url('/blog/' . $post->slug) }}" class="group bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-gray-700 transition duration-300">
                <div class="aspect-[16/9] bg-gray-800 overflow-hidden">
                    @if($post->image_url)
                        @php $postImg = str_replace(['/w1280/', '/w780/'], '/w500/', $post->image_url); @endphp
                        <img src="{{ $postImg }}" alt="{{ $post->title }}" loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-700">📝</div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-2 py-0.5 bg-rose-600/20 text-rose-400 text-xs rounded-full">{{ $post->category }}</span>
                        <span class="text-gray-500 text-xs">{{ $post->read_time }} dk okuma</span>
                    </div>
                    <h2 class="text-white font-semibold mb-2 group-hover:text-rose-400 transition line-clamp-2">
                        {{ $post->title }}
                    </h2>
                    <p class="text-gray-400 text-sm line-clamp-2">
                        {{ $post->excerpt }}
                    </p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-800">
                        <span class="text-gray-500 text-xs">{{ $post->published_at->format('d.m.Y') }}</span>
                        <span class="text-rose-400 text-xs group-hover:translate-x-1 transition inline-flex items-center gap-1">
                            Oku <span>→</span>
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection
