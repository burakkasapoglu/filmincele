<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="text-center">
                <x-logo />
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="E-posta" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Şifre" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-400">Beni hatırla</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-400 hover:text-gray-300 rounded-md" href="{{ route('password.request') }}">
                        Şifreni mi unuttun?
                    </a>
                @endif

                <x-button class="ms-4">
                    Giriş Yap
                </x-button>
            </div>
        </form>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-900 text-gray-400">veya</span>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <a href="{{ url('/auth/google/redirect') }}" class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-700 rounded-lg text-sm text-white hover:bg-gray-800 transition">
                    🇬 Google
                </a>
                <a href="{{ url('/auth/github/redirect') }}" class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-700 rounded-lg text-sm text-white hover:bg-gray-800 transition">
                    🐙 GitHub
                </a>
            </div>
        </div>

        <div class="mt-4 text-center">
            <span class="text-gray-400 text-sm">Hesabın yok mu?</span>
            <a href="{{ url('/kayit') }}" class="text-sm text-rose-400 hover:underline ml-1">Kayıt ol</a>
        </div>
    </x-authentication-card>
</x-guest-layout>
