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
        $today = now()->format('d.m.Y');
        $linkableMovies = $this->findLinkableMovies($topic);

        $linkSection = '';
        if (!empty($linkableMovies)) {
            $linkSection = "\n\nASAGIDAKI FILMLER SITENDE MEVCUT VE LINKLI ANILMALI (markDown linkleri hazir, oldugu gibi kullan):\n";
            foreach ($linkableMovies as $lm) {
                $slug = \Illuminate\Support\Str::slug($lm['title']);
                $linkSection .= "- [{$lm['title']}](/film/{$lm['id']}-{$slug})\n";
            }
            $linkSection .= "Bu listeden en az " . min(5, count($linkableMovies)) . " tanesini yazinin icinde dogal sekilde kullan. Bu filmler haricinde baska filme link ekleme — sadece isimlerini kalin metin olarak yazabilirsin.";
        }

        $prompt = "Sen filmincele.com sinema blog yazari. Bugün tarih: {$today}.

GÖREV: \"{$topic}\" konusunda SEO uyumlu, uzun ve detayli bir Turkce blog yazisi yaz.

KURALLAR:
- Baslik dikkat cekici olsun
- En az 5-6 paragraf yaz
- Film isimlerini **[Film Adi](/film/TMDB_ID-slug)** formatinda filmincele.com ic linkiyle ver
- Verisi olmayan (yakinda cikacak, hic oyu olmayan) filmlere KESINLIKLE link verme, sadece kalin metin yaz
- Linkler sadece filmincele.com'a olsun, baska siteye link verme (IMDB vb YASAK)
- Kategori: Haber, Liste, Analiz, Rehber'den birini sec
- **SADECE JSON dondur**, baska metin ekleme{$linkSection}

JSON format: {\"title\":\"Baslik\",\"excerpt\":\"ozet\",\"body\":\"markdown icerik\",\"category\":\"Liste\",\"image_query\":\"gonrsel icin aranacak film/oyuncu adi\"}";

        try {
            $response = Http::timeout(30)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 4000],
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

            if (!$result || !isset($result['title'], $result['body'])) return null;

            $result['body'] = $this->fixMovieLinks($result['body']);

            return $result;
        } catch (\Exception $e) {
            Log::warning('Gemini exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Konuyla ilgili TMDB'de verisi dolu (linklemeye layik) filmleri bul.
     */
    private function findLinkableMovies(string $topic, int $min = 10): array
    {
        $tmdb = app(TmdbService::class);

        $pools = [
            $tmdb->searchMulti($topic),
            $tmdb->getTrending('week'),
            $tmdb->getPopularMovies(),
        ];

        $seen = [];
        $out = [];
        foreach ($pools as $pool) {
            foreach ($pool as $item) {
                $id = $item['id'] ?? null;
                $title = $item['title'] ?? $item['name'] ?? null;
                if (!$id || !$title || isset($seen[$id])) continue;
                $seen[$id] = true;

                $votes = (int) ($item['vote_count'] ?? 0);
                $hasOverview = !empty(trim((string) ($item['overview'] ?? '')));
                $poster = !empty($item['poster_path']);

                if ($votes >= 20 && $hasOverview && $poster) {
                    $out[] = ['id' => $id, 'title' => $title];
                }
                if (count($out) >= $min) return $out;
            }
        }

        return $out;
    }

    private function fixMovieLinks(string $body): string
    {
        return preg_replace_callback(
            '/\[([^\]]+)\]\(\/film\/(\d+)-[^)]*\)/',
            function ($m) {
                $title = $m[1];
                $id = (int) $m[2];

                $correctId = $this->resolveMovieId($title, $id);

                if ($correctId === null) {
                    return $title;
                }

                $slug = \Illuminate\Support\Str::slug($this->normalizeTitle($title));
                return "[{$title}](/film/{$correctId}-{$slug})";
            },
            $body
        );
    }

    private function resolveMovieId(string $title, int $claimedId): ?int
    {
        $tmdb = app(TmdbService::class);

        $details = $tmdb->getMovieDetails($claimedId);
        if (
            $details
            && $this->titlesMatch($title, $details['title'] ?? '', $details['original_title'] ?? '')
            && $this->hasEnoughData($details)
        ) {
            return $claimedId;
        }

        $results = $tmdb->searchMovies($this->normalizeTitle($title));
        foreach ($results as $r) {
            if ($this->titlesMatch($title, $r['title'] ?? '', $r['original_title'] ?? '') && $this->hasEnoughData($r)) {
                return $r['id'];
            }
        }

        foreach ($results as $r) {
            if ($this->hasEnoughData($r) && $this->slugifyTitle($r['title'] ?? '') === $this->slugifyTitle($title)) {
                return $r['id'];
            }
        }

        return null;
    }

    /**
     * Kayit gercekten dolu mu? Bos/istisna kayitlar (az oy, ozetsiz) linke layik degil.
     */
    private function hasEnoughData(array $movie): bool
    {
        $votes = (int) ($movie['vote_count'] ?? 0);
        $hasOverview = !empty(trim((string) ($movie['overview'] ?? '')));

        return $votes >= 20 && $hasOverview;
    }

    private function titlesMatch(string $linkTitle, string ...$candidates): bool
    {
        $link = $this->slugifyTitle($linkTitle);
        foreach ($candidates as $c) {
            if ($c && $this->slugifyTitle($c) === $link) {
                return true;
            }
        }
        return false;
    }

    private function slugifyTitle(string $title): string
    {
        $title = $this->normalizeTitle($title);
        return \Illuminate\Support\Str::slug($title);
    }

    private function normalizeTitle(string $title): string
    {
        return trim(preg_replace('/^(dizisi|filmi|filmi|yapımı)\s*$/iu', '', $title));
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
        $body .= "**{$cleanTopic}** hakkında kapsamlı bir inceleme hazırladık. İşte bilmeniz gereken her şey ve en iyi yapımlar:\n\n";

        if (!empty($movies)) {
            $body .= "## Öne Çıkan Yapımlar\n\n";
            foreach ($movies as $i => $movie) {
                $mTitle = $movie['title'] ?? $movie['name'] ?? '';
                $slug = \Illuminate\Support\Str::slug($mTitle);
                $year = substr($movie['release_date'] ?? '—', 0, 4);
                $rating = number_format($movie['vote_average'] ?? 0, 1);
                $votes = number_format($movie['vote_count'] ?? 0);

                $body .= "### " . ($i + 1) . ". {$mTitle} ({$year})\n\n";
                $body .= "⭐ **IMDb Puanı:** {$rating}/10 ({$votes} oy)\n\n";
                if (!empty($movie['overview'])) {
                    $body .= "{$movie['overview']}\n\n";
                }
            }

            // Best of the best section
            $topMovie = $movies[0] ?? null;
            if ($topMovie) {
                $body .= "## Neden İzlemelisiniz?\n\n";
                $body .= "Listemizin zirvesinde yer alan **{$topMovie['title']}**, {$rating} puanla kullanıcıların beğenisini kazanmış durumda. ";
                $body .= "Bu türdeki en iyi örneklerden biri olarak kabul ediliyor.\n\n";

                // Similar movies
                $similar = $tmdb->getMovieRecommendations($topMovie['id']);
                $similar = array_slice($similar, 0, 5);
                if (!empty($similar)) {
                    $body .= "## Bunları da Sevebilirsiniz\n\n";
                    foreach ($similar as $m) {
                        $mTitle = $m['title'] ?? $m['name'] ?? '';
                        $mSlug = \Illuminate\Support\Str::slug($mTitle);
                        $body .= "- **[{$mTitle}](/film/{$m['id']}-{$mSlug})** — ★ " . number_format($m['vote_average'] ?? 0, 1) . "\n";
                    }
                    $body .= "\n";
                }
            }

            // Trending in genre
            $topGenres = $movies[0]['genre_ids'] ?? [];
            if (!empty($topGenres)) {
                $genreMovies = $tmdb->discoverByGenres(array_slice($topGenres, 0, 2), 1, 'popularity.desc');
                $genreMovies = array_slice($genreMovies, 0, 4);
                if (!empty($genreMovies)) {
                    $body .= "## Aynı Türdeki Popüler Yapımlar\n\n";
                    foreach ($genreMovies as $m) {
                        $mTitle = $m['title'] ?? $m['name'] ?? '';
                        $mSlug = \Illuminate\Support\Str::slug($mTitle);
                        $body .= "- **[{$mTitle}](/film/{$m['id']}-{$mSlug})** — ★ " . number_format($m['vote_average'] ?? 0, 1) . "\n";
                    }
                    $body .= "\n";
                }
            }
        }

        $body .= "---\n\n";
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

        $candidates = array_merge(
            $tmdb->searchMulti($query),
            $tmdb->searchMovies($query)
        );

        $images = [];
        foreach ($candidates as $item) {
            $path = $item['poster_path'] ?? $item['profile_path'] ?? null;
            if ($path) {
                $images[] = 'https://image.tmdb.org/t/p/w780' . $path;
            }
            if (count($images) >= 8) break;
        }

        if (!empty($images)) {
            // Ayni sorgu icin deterministik ama farkli gunlerde farkli resim
            return $images[now()->dayOfYear % count($images)];
        }

        $popular = [];
        foreach ($tmdb->getPopularMovies() as $item) {
            if (!empty($item['poster_path'])) {
                $popular[] = 'https://image.tmdb.org/t/p/w780' . $item['poster_path'];
            }
            if (count($popular) >= 8) break;
        }

        return $popular[now()->dayOfYear % max(count($popular), 1)] ?? null;
    }
}
