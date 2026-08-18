<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VideoScriptService
{
    private ?string $apiKey = null;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?: null;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Sosyal medya videosu için konuşma metni + çekim planı üret.
     */
    public function generateScript(string $topic): ?array
    {
        if (!$this->isConfigured()) return null;

        $prompt = "Sen filmincele.com'un sosyal medya video içerik üreticisisin. Kanal sahibi kamera önünde konuşan bir sunucu; videolar Instagram Reels / TikTok / YouTube Shorts formatında.

KONU: {$topic}

GÖREV: Bu konu için 60-90 saniyelik bir video metni hazırla. Blog yazisi DEGIL, birebir kamerada okunacak konuşma metni olmali.

KURALLAR:
- Ilk 3 saniyede izleyiciyi yakalayan bir 'hook' cumlesi ile basla (soru, iddia veya sok edici bilgi)
- Konusma dili kullan: kisa cumleler, sogukkanli ve meraklandirici ton, samimi anlatim
- Sunucu birinci agizdan konussun ('Bugun size gosterecegim...' gibi)
- Somut bilgi ver: yil, sayi, ilginç detay, kisa ornekler
- Sonunda izleyiciye cagri yap: yorum/yorumda tartisma/takip ('Sence hangisi?' gibi dogal bir soru)
- Film adi gecerken cekim notunda hangi sahnenin gosterilecegini belirt
- Video basligi kisa ve meraklandirici olsun (max 60 karakter)
- Sosyal medya aciklamasi 1-2 cumle + 8-12 hashtag ( Turkce ağırlıklı)

SADECE JSON dondur: {\"video_title\":\"...\",\"hook\":\"ilk 3 saniye cumlesi\",\"script\":\"konusma metni (paragraflar halinde)\",\"visual_notes\":[\"sahne 1: ...\",\"sahne 2: ...\"],\"sm_caption\":\"sosyal medya aciklamasi\",\"hashtags\":[\"#...\",\"#...\"]}";

        try {
            $response = Http::timeout(60)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.8, 'maxOutputTokens' => 4000],
            ]);

            if (!$response->successful()) {
                Log::warning('VideoScript Gemini HTTP failed', ['status' => $response->status()]);
                return null;
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) return null;

            $text = trim(str_replace(['```json', '```'], '', $text));
            $result = json_decode($text, true);

            return ($result && isset($result['video_title'], $result['script'])) ? $result : null;
        } catch (\Exception $e) {
            Log::warning('VideoScript exception: ' . $e->getMessage());
            return null;
        }
    }
}
