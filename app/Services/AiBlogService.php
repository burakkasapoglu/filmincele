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
        $result = null;

        if ($this->isConfigured()) {
            $result = $this->tryGemini($topic);
        }

        return $result ?? $this->generateFallback($topic);
    }

    private function tryGemini(string $topic): ?array
    {
        $prompt = "Sen bir sinema blog yazarısın. \"{$topic}\" konusunda SEO uyumlu, ilgi çekici bir Türkçe blog yazısı yaz. Yanıtını JSON formatında ver: {\"title\": \"Başlık\", \"excerpt\": \"Kısa özet\", \"body\": \"Markdown içerik\", \"category\": \"Liste|Haber|Analiz|Rehber\"}. SADECE JSON döndür.";

        try {
            $response = Http::timeout(25)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 1500],
            ]);

            if (!$response->successful()) return null;

            $text = $response->json('candidates.0.content.parts.0.text');
            if (!$text) return null;

            $text = trim(str_replace(['```json', '```'], '', $text));
            $result = json_decode($text, true);

            return ($result && isset($result['title'], $result['body'])) ? $result : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function generateFallback(string $topic): array
    {
        $tmdb = app(TmdbService::class);
        $movies = array_merge(
            $tmdb->searchMovies($topic),
            $tmdb->getPopularMovies()
        );
        $movies = array_slice($movies, 0, 8);

        $title = trim($topic) . ' Hakkında Bilmeniz Gerekenler';
        $excerpt = '"' . trim($topic) . '" konusunda en iyi filmler, detaylı inceleme ve öneriler.';

        $body = "# {$topic} Hakkında Bilmeniz Gerekenler\n\n";
        $body .= "Bu yazıda **{$topic}** konusunu mercek altına alıyoruz. İşte karşınızda en dikkat çekici yapımlar:\n\n";

        if (!empty($movies)) {
            foreach ($movies as $i => $movie) {
                $mTitle = $movie['title'] ?? $movie['name'] ?? '';
                $body .= ($i + 1) . ". **[$mTitle](/film/{$movie['id']}-" . \Illuminate\Support\Str::slug($mTitle) . ")** ";
                $body .= "— ★ " . number_format($movie['vote_average'] ?? 0, 1) . "\n";
                if (!empty($movie['overview'])) {
                    $body .= \Illuminate\Support\Str::limit($movie['overview'], 120) . "\n";
                }
                $body .= "\n";
            }
        }

        $body .= "Daha fazla film keşfetmek için [ana sayfamızı](/) ziyaret edin! 🎬";

        return [
            'title' => $title,
            'excerpt' => $excerpt,
            'body' => $body,
            'category' => 'Liste',
            'image_query' => $topic,
        ];
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
