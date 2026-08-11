<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiBlogService
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
        $prompt = "Sen sinema blog yazari. KISA yaz. SADECE JSON dondur, baska metin ekleme: {\"title\":\"Baslik\",\"excerpt\":\"ozet\",\"body\":\"markdown icerik\",\"category\":\"Haber\"}. Konu: {$topic}";

        try {
            $response = Http::timeout(20)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 1500],
            ]);

            if (!$response->successful()) {
                Log::warning('Gemini HTTP failed', ['status' => $response->status()]);
                return null;
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) return null;

            $text = trim(str_replace(['```json', '```'], '', $text));
            $result = json_decode($text, true);

            return ($result && isset($result['title'], $result['body'])) ? $result : null;
        } catch (\Exception $e) {
            Log::warning('Gemini exception: ' . $e->getMessage());
            return null;
        }
    }

    private function generateFallback(string $topic): array
    {
        $tmdb = app(TmdbService::class);
        $cleanTopic = trim($topic);

        // Search for movies related to the topic
        $movies = $tmdb->searchMovies($cleanTopic);
        $movies = array_slice($movies, 0, 6);

        $title = ucfirst($cleanTopic);
        if (mb_strlen($title) > 70) $title = mb_substr($title, 0, 67) . '...';

        $excerpt = "\"{$cleanTopic}\" hakkında detaylı inceleme, en iyi yapımlar ve öneriler.";

        $body = "# {$title}\n\n";
        $body .= "**{$cleanTopic}** hakkında kapsamlı bir inceleme hazırladık. İşte bilmeniz gerekenler ve en iyi yapımlar:\n\n";

        if (!empty($movies)) {
            $body .= "## Öne Çıkan Yapımlar\n\n";
            foreach ($movies as $i => $movie) {
                $mTitle = $movie['title'] ?? $movie['name'] ?? '';
                $slug = \Illuminate\Support\Str::slug($mTitle);
                $year = substr($movie['release_date'] ?? '—', 0, 4);
                $rating = number_format($movie['vote_average'] ?? 0, 1);

                $body .= ($i + 1) . ". **[{$mTitle}](/film/{$movie['id']}-{$slug})** ({$year}) — ★ {$rating}\n";
                if (!empty($movie['overview'])) {
                    $body .= "> " . \Illuminate\Support\Str::limit($movie['overview'], 200) . "\n";
                }
                $body .= "\n";
            }

            // Add similar/recommended from first movie
            $firstMovieId = $movies[0]['id'] ?? null;
            if ($firstMovieId) {
                $similar = $tmdb->getMovieRecommendations($firstMovieId);
                $similar = array_slice($similar, 0, 4);
                if (!empty($similar)) {
                    $body .= "## Benzer Öneriler\n\n";
                    foreach ($similar as $m) {
                        $mTitle = $m['title'] ?? $m['name'] ?? '';
                        $mSlug = \Illuminate\Support\Str::slug($mTitle);
                        $body .= "- **[{$mTitle}](/film/{$m['id']}-{$mSlug})** — ★ " . number_format($m['vote_average'] ?? 0, 1) . "\n";
                    }
                    $body .= "\n";
                }
            }
        }

        $body .= "Bu yapımları **Filmincele** üzerinden puanlayabilir, listelerinize ekleyebilir ve arkadaşlarınızla paylaşabilirsiniz.\n\n";
        $body .= "Daha fazlası için [ana sayfamızı](/) ve [keşfet](/kesfet) sayfamızı ziyaret edin! 🎬";

        return [
            'title' => $title,
            'excerpt' => $excerpt,
            'body' => $body,
            'category' => 'Haber',
            'image_query' => $movies[0]['title'] ?? $cleanTopic,
        ];
    }

    public function searchImage(string $query): ?string
    {
        $tmdb = app(TmdbService::class);

        $results = $tmdb->searchMovies($query);
        if (empty($results)) {
            $words = explode(' ', $query);
            $results = $tmdb->searchMovies($words[0] ?? $query);
        }
        if (empty($results)) {
            $results = $tmdb->getPopularMovies();
        }

        if (!empty($results) && isset($results[0]['poster_path'])) {
            return 'https://image.tmdb.org/t/p/w780' . $results[0]['poster_path'];
        }
        return null;
    }
}
