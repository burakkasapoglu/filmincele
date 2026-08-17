@props(['user' => null, 'size' => 'w-20 h-20', 'textSize' => 'text-xl'])

@php
$u = $user ?? Auth::user();
$initial = $u ? strtoupper(substr($u->name, 0, 1)) : '?';
@endphp

@if($u && $u->profile_photo_path)
    <img src="{{ $u->profile_photo_url }}" class="{{ $size }} rounded-2xl object-cover {{ $attributes->get('class') }}" alt="{{ $u->name }}">
@else
    <div class="{{ $size }} rounded-2xl bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center relative overflow-hidden {{ $attributes->get('class') }}">
        <svg viewBox="0 0 100 100" class="w-3/4 h-3/4 text-white/30 absolute opacity-30">
            <path fill="currentColor" d="M20 80 L20 40 Q20 20 50 20 Q80 20 80 40 L80 80 L70 80 L70 45 Q70 40 68 40 L60 40 L60 80 L40 80 L40 40 L32 40 Q30 40 30 45 L30 80 Z"/>
            <rect fill="currentColor" x="20" y="80" width="60" height="8" rx="2"/>
            <rect fill="currentColor" x="10" y="88" width="8" height="12" rx="2"/>
            <rect fill="currentColor" x="82" y="88" width="8" height="12" rx="2"/>
        </svg>
        <span class="text-white {{ $textSize }} font-bold relative z-10">{{ $initial }}</span>
    </div>
@endif
