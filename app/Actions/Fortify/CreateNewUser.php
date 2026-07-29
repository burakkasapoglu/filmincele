<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'birth_date' => ['required', 'date', 'before_or_equal:' . now()->subYears(13)->format('Y-m-d')],
        ], [
            'birth_date.required' => 'Doğum tarihi zorunludur.',
            'birth_date.before_or_equal' => 'En az 13 yaşında olmalısınız.',
        ])->validate();

        $this->verifyRecaptcha($input['g-recaptcha-response'] ?? '');

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'birth_date' => $input['birth_date'],
        ]);
    }

    private function verifyRecaptcha(string $token): void
    {
        $secret = config('services.recaptcha.secret');

        if (empty($secret) || empty($token)) {
            return;
        }

        try {
            $response = Http::timeout(5)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
            ]);

            if (!$response->json('success', false)) {
                throw new \Exception('reCAPTCHA doğrulaması başarısız.');
            }
        } catch (\Exception $e) {
        }
    }
}
