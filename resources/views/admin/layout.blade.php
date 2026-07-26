<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="antialiased bg-gray-950 text-white">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="hidden md:flex md:flex-col w-72 bg-gray-900/80 backdrop-blur-md border-r border-gray-800/50">
            <div class="p-6">
                <x-logo />
                <p class="text-gray-500 text-xs mt-1 ml-9">Admin Paneli</p>
            </div>

            <div class="px-4 mb-4">
                <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-rose-400 text-xs">Yönetici</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-3 space-y-1">
                <p class="text-gray-600 text-xs uppercase tracking-wider px-3 py-2 font-semibold">Ana Menü</p>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200
                          {{ request()->routeIs('admin.dashboard')
                              ? 'bg-rose-600/20 text-rose-400 font-medium border border-rose-600/20 shadow-lg shadow-rose-600/5'
                              : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                    <span class="text-lg flex-shrink-0">📊</span>
                    <span>Dashboard</span>
                    @if(request()->routeIs('admin.dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    @endif
                </a>

                <a href="{{ route('admin.users') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200
                          {{ request()->routeIs('admin.users*')
                              ? 'bg-rose-600/20 text-rose-400 font-medium border border-rose-600/20 shadow-lg shadow-rose-600/5'
                              : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                    <span class="text-lg flex-shrink-0">👥</span>
                    <span>Üyeler</span>
                    @if(request()->routeIs('admin.users*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    @endif
                </a>

                <a href="{{ route('admin.posts') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200
                          {{ request()->routeIs('admin.posts*')
                              ? 'bg-rose-600/20 text-rose-400 font-medium border border-rose-600/20 shadow-lg shadow-rose-600/5'
                              : 'text-gray-400 hover:text-white hover:bg-gray-800/50' }}">
                    <span class="text-lg flex-shrink-0">📝</span>
                    <span>Blog</span>
                    @if(request()->routeIs('admin.posts*'))
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    @endif
                </a>

                <div class="border-t border-gray-800/50 my-4"></div>
                <p class="text-gray-600 text-xs uppercase tracking-wider px-3 py-2 font-semibold">Bağlantılar</p>

                <a href="{{ url('/') }}" target="_blank"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-gray-800/50 transition-all duration-200">
                    <span class="text-lg flex-shrink-0">🌐</span>
                    <span>Siteye Git</span>
                    <span class="ml-auto text-gray-600">↗</span>
                </a>

                <a href="{{ url('/kvkk') }}" target="_blank"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-gray-800/50 transition-all duration-200">
                    <span class="text-lg flex-shrink-0">🔒</span>
                    <span>KVKK Sayfası</span>
                    <span class="ml-auto text-gray-600">↗</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-gray-500 hover:text-red-400 hover:bg-gray-800/50 transition-all duration-200">
                        <span class="text-lg flex-shrink-0">🚪</span>
                        <span>Çıkış Yap</span>
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 min-w-0 bg-gray-950">
            {{-- Mobile Header --}}
            <div class="md:hidden bg-gray-900/80 backdrop-blur-md border-b border-gray-800/50 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
                <x-logo />
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users') }}" class="text-xs {{ request()->routeIs('admin.users*') ? 'text-rose-400' : 'text-gray-400' }}">Üyeler</a>
                    <a href="{{ route('admin.posts') }}" class="text-xs {{ request()->routeIs('admin.posts*') ? 'text-rose-400' : 'text-gray-400' }}">Blog</a>
                    <a href="{{ url('/') }}" class="text-xs text-gray-500">Site</a>
                </div>
            </div>

            <div class="p-6 md:p-8">
                @if(session('success'))
                    <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm">
                        <span>✓</span> {{ session('success') }}
                    </div>
                @endif
                @yield('admin-content')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
