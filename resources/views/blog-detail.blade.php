@extends('layouts.app')
@section('title', $post->title . ' — Filmincele Blog')

@section('content')
<article class="max-w-3xl mx-auto px-4 py-8">
    <a href="{{ url('/blog') }}" class="text-gray-400 hover:text-white text-sm mb-6 inline-flex items-center gap-1 transition">
        ← Bloga Dön
    </a>

    <div class="mb-6">
        <div class="flex items-center gap-3 mb-3">
            <span class="px-2 py-0.5 bg-rose-600/20 text-rose-400 text-xs rounded-full">{{ $post->category }}</span>
            <span class="text-gray-500 text-xs">{{ $post->read_time }} dk okuma</span>
            <span class="text-gray-500 text-xs">·</span>
            <span class="text-gray-500 text-xs">{{ $post->published_at->format('d.m.Y') }}</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ $post->title }}</h1>
        @if($post->excerpt)
            <p class="text-gray-400 text-lg leading-relaxed">{{ $post->excerpt }}</p>
        @endif
    </div>

    @if($post->image_url)
        <div class="aspect-[16/9] rounded-2xl overflow-hidden bg-gray-800 mb-8">
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}"
                 class="w-full h-full object-cover">
        </div>
    @endif

    <div class="prose prose-invert prose-rose max-w-none
        prose-headings:text-white prose-h2:text-2xl prose-h2:font-bold prose-h2:mt-10 prose-h2:mb-4
        prose-h3:text-xl prose-h3:font-semibold prose-h3:mt-8 prose-h3:mb-3
        prose-p:text-gray-300 prose-p:leading-relaxed prose-p:my-4
        prose-strong:text-white prose-strong:font-semibold
        prose-a:text-rose-400 prose-a:no-underline hover:prose-a:underline
        prose-li:text-gray-300 prose-li:leading-relaxed
        prose-ul:my-3 prose-ol:my-3
        prose-ul:pl-5 prose-ol:pl-5">
        {!! \Illuminate\Support\Str::markdown($post->body) !!}
    </div>

    <div class="mt-12 pt-6 border-t border-gray-800">
        <a href="{{ url('/blog') }}" class="text-rose-400 hover:text-rose-300 transition text-sm">
            ← Tüm Yazılar
        </a>
    </div>
</article>
@endsection
