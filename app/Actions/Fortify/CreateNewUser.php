<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\RecaptchaService;
use Illuminate\Support\Facades\Hash;
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
            'g-recaptcha-response' => [function ($attr, $value, $fail) {
                try {
                    $recaptcha = new RecaptchaService();
                    if ($recaptcha->isConfigured() && !$recaptcha->verify($value ?? '')) {
                        $fail('Lütfen robot olmadığınızı doğrulayın.');
                    }
                } catch (\Exception $e) {}
            }],
            'website' => ['nullable', 'string', 'max:0'],
        ], [
            'birth_date.required' => 'Doğum tarihi zorunludur.',
            'birth_date.before_or_equal' => 'En az 13 yaşında olmalısınız.',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'birth_date' => $input['birth_date'],
        ]);
    }
}
