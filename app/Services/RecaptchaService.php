<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    private string $secret;
    private string $siteKey;

    public function __construct()
    {
        $this->secret = config('services.recaptcha.secret', '');
        $this->siteKey = config('services.recaptcha.site_key', '');
    }

    public function siteKey(): string
    {
        return $this->siteKey;
    }

    public function isConfigured(): bool
    {
        return !empty($this->secret) && !empty($this->siteKey);
    }

    public function verify(string $token): bool
    {
        if (!$this->isConfigured()) return true;

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $this->secret,
            'response' => $token,
        ]);

        return $response->json('success', false);
    }
}
