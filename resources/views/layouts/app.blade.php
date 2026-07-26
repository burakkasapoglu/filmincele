<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Filmincele') — Filmincele</title>
    <x-seo :title="$__env->yieldContent('title', 'Filmincele') . ' — Filmincele'" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-950 text-white">
    <nav class="bg-gray-900/80 backdrop-blur-md border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <x-logo />
                    <div class="hidden lg:flex items-center gap-6">
                        <a href="{{ url('/') }}" class="text-sm {{ request()->is('/') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} transition">
                            Ana Sayfa
                        </a>
                        <a href="{{ url('/vizyonda') }}" class="text-sm {{ request()->is('vizyonda') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} transition">
                            Vizyonda
                        </a>
                        <a href="{{ url('/yakinda') }}" class="text-sm {{ request()->is('yakinda') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} transition">
                            Yakında
                        </a>
                        <a href="{{ url('/mod/dram?mediaType=tv') }}" class="text-sm text-gray-300 hover:text-white transition">
                            Diziler
                        </a>

                        {{-- Mega Dropdown: Türler + Koleksiyonlar --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="text-sm {{ request()->is('mod/*') || request()->is('koleksiyon/*') || request()->is('kesfet') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} transition flex items-center gap-1 cursor-pointer">
                                Keşfet
                                <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms class="absolute top-full left-0 pt-1 z-50">
                                <div class="w-[480px] bg-gray-900 border border-gray-800 rounded-2xl shadow-xl overflow-hidden p-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        {{-- Türler --}}
                                        <div>
                                            <p class="text-gray-500 text-xs uppercase tracking-wide mb-2 px-2">Türler</p>
                                            <div class="grid grid-cols-2 gap-0.5">
                                                @foreach(config('moods') as $slug => $mood)
                                                    <a href="{{ url('/mod/' . $slug) }}" class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-xs text-gray-300 hover:bg-gray-800 hover:text-white transition">
                                                        <span>{{ $mood['emoji'] }}</span> {{ $mood['label'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        {{-- Koleksiyonlar + Özel --}}
                                        <div>
                                            <p class="text-gray-500 text-xs uppercase tracking-wide mb-2 px-2">Koleksiyonlar</p>
                                            @foreach(config('collections') as $slug => $coll)
                                                <a href="{{ url('/koleksiyon/' . $slug) }}" class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-xs text-gray-300 hover:bg-gray-800 hover:text-white transition">
                                                    <span>{{ $coll['emoji'] }}</span> {{ $coll['name'] }}
                                                </a>
                                            @endforeach
                                            <div class="border-t border-gray-800 my-2"></div>
                                            <a href="{{ url('/mod/turk') }}" class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-xs text-gray-300 hover:bg-gray-800 hover:text-white transition">
                                                🇹🇷 Türk Yapımları
                                            </a>
                                            <a href="{{ url('/kesfet') }}" class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-xs text-rose-400 hover:bg-gray-800 transition">
                                                🔍 Gelişmiş Arama
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ url('/blog') }}" class="text-sm {{ request()->is('blog*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} transition">
                            Blog
                        </a>
                        <a href="{{ url('/karsilastir') }}" class="text-sm {{ request()->is('karsilastir*') ? 'text-white font-semibold' : 'text-gray-300 hover:text-white' }} transition">
                            Karşılaştır
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:block w-56">
                        <livewire:search-bar />
                    </div>
                    @auth
                        <a href="{{ url('/profil') }}" class="flex items-center gap-2 text-sm text-gray-300 hover:text-white transition">
                            <div class="w-8 h-8 rounded-full bg-rose-600 flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:inline">Profil</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-400 hover:text-white transition">
                                Çıkış
                            </button>
                        </form>
                    @else
                        <a href="{{ url('/giris') }}" class="text-sm text-gray-300 hover:text-white transition">
                            Giriş
                        </a>
                        <a href="{{ url('/kayit') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-sm font-medium rounded-lg transition">
                            Kayıt Ol
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        {{-- Mobile nav --}}
        <div class="lg:hidden border-t border-gray-800 overflow-x-auto">
            <div class="flex gap-1.5 px-4 py-2">
                <a href="{{ url('/') }}" class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs {{ request()->is('/') ? 'bg-rose-600 text-white' : 'bg-gray-800 text-gray-300' }}">Ana Sayfa</a>
                <a href="{{ url('/vizyonda') }}" class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs {{ request()->is('vizyonda') ? 'bg-rose-600 text-white' : 'bg-gray-800 text-gray-300' }}">Vizyonda</a>
                <a href="{{ url('/yakinda') }}" class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs {{ request()->is('yakinda') ? 'bg-rose-600 text-white' : 'bg-gray-800 text-gray-300' }}">Yakında</a>
                <a href="{{ url('/mod/dram?mediaType=tv') }}" class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs bg-gray-800 text-gray-300">Diziler</a>
                <a href="{{ url('/mod/turk') }}" class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs bg-gray-800 text-gray-300">🇹🇷 Türk</a>
                <a href="{{ url('/kesfet') }}" class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs bg-gray-800 text-gray-300">Keşfet</a>
                <a href="{{ url('/blog') }}" class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs bg-gray-800 text-gray-300">Blog</a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-gray-800 py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm space-y-2">
            <p>&copy; {{ date('Y') }} filmincele.com — Film verileri <a href="https://www.themoviedb.org/" class="text-rose-400 hover:underline" target="_blank">TMDB</a> tarafından sağlanmaktadır.</p>
            <p>
                <a href="{{ url('/kvkk') }}" class="text-gray-400 hover:text-white transition">KVKK Aydınlatma Metni</a>
                @auth
                    @if(Auth::user()->is_admin)
                        · <a href="{{ url('/admin') }}" class="text-rose-400 hover:text-rose-300 transition">Admin Panel</a>
                    @endif
                @endauth
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
