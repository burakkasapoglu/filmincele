<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiBlogService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateBlogPost(string $topic): ?array
    {
        if (!$this->isConfigured()) return null;

        $prompt = "Sen bir sinema blog yazarısın. Türkçe olarak \"{$topic}\" konusunda SEO uyumlu, ilgi çekici bir blog yazısı yaz.

Yanıtını JSON formatında ver:
{
  \"title\": \"Blog başlığı (max 80 karakter)\",
  \"excerpt\": \"Kısa özet (max 150 karakter)\",
  \"body\": \"Markdown formatında blog içeriği. Başlıklar için ## kullan. Film isimleri için **[Film Adı](/film/tmdb_id-fim-adi)** formatını kullan.\",
  \"category\": \"Kategori (Liste, Haber, Analiz, Rehber, Eğlence, Tartışma)\",
  \"image_query\": \"Görsel araması için İngilizce 2-3 kelimelik anahtar kelime\"
}

Kurallar:
- Başlık dikkat çekici olsun
- En az 4-5 paragraf yaz
- Film/dizi isimlerini **[Film Adı](/film/ID-slug)** formatında linkle
- Kategori uygun olsun
- SEO için anahtar kelimeler kullan
- SADECE JSON döndür, başka metin ekleme";

        try {
            $response = Http::timeout(30)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [[
                    'parts' => [['text' => $prompt]]
                ]],
                'generationConfig' => [
                    'temperature' => 0.8,
                    'maxOutputTokens' => 2048,
                ],
            ]);

            if (!$response->successful()) return null;

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) return null;

            $text = trim(str_replace(['```json', '```'], '', $text));
            $result = json_decode($text, true);

            if (!$result || !isset($result['title'], $result['body'])) return null;

            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function searchImage(string $query): ?string
    {
        $tmdb = app(TmdbService::class);
        $results = $tmdb->searchMovies($query);
        if (!empty($results) && isset($results[0]['poster_path'])) {
            return 'https://image.tmdb.org/t/p/w780' . $results[0]['poster_path'];
        }
        return null;
    }
}
