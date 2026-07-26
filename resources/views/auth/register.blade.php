<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="text-center">
                <x-logo />
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="Ad Soyad" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="E-posta" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Şifre" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="Şifre Tekrar" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="birth_date" value="Doğum Tarihi" />
                <x-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" required max="{{ now()->subYears(13)->format('Y-m-d') }}" />
                <p class="text-gray-500 text-xs mt-1">+18 içerikler için yaşınızı doğrulamak amacıyla gereklidir.</p>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />
                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-400 hover:text-gray-300">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-400 hover:text-gray-300">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <x-button class="ms-4">
                    Kayıt Ol
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
            <span class="text-gray-400 text-sm">Zaten hesabın var mı?</span>
            <a href="{{ url('/giris') }}" class="text-sm text-rose-400 hover:underline ml-1">Giriş yap</a>
        </div>
    </x-authentication-card>
</x-guest-layout>
