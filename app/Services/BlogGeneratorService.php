<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Log;

class BlogGeneratorService
{
    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function generate(): ?Post
    {
        $types = ['birthday', 'trending', 'anniversary', 'upcoming', 'recommendation'];
        $type = $types[array_rand($types)];

        return match ($type) {
            'birthday' => $this->generateBirthdayPost(),
            'trending' => $this->generateTrendingPost(),
            'anniversary' => $this->generateAnniversaryPost(),
            'upcoming' => $this->generateUpcomingPost(),
            'recommendation' => $this->generateRecommendationPost(),
            default => null,
        };
    }

    private function generateBirthdayPost(): ?Post
    {
        $today = now()->format('m-d');
        $birthdays = collect(config('cinema-calendar'))
            ->filter(fn($p) => substr($p['birthday'], 5) === $today)
            ->values();

        if ($birthdays->isEmpty()) {
            $yesterday = now()->subDay()->format('m-d');
            $birthdays = collect(config('cinema-calendar'))
                ->filter(fn($p) => substr($p['birthday'], 5) === $yesterday)
                ->values();
        }

        if ($birthdays->isEmpty()) return null;

        $people = [];
        foreach ($birthdays->take(5) as $person) {
            $details = $this->tmdb->getPersonDetails($person['tmdb_id']);
            $age = \Carbon\Carbon::parse($person['birthday'])->age;
            $people[] = [
                'name' => $person['name'],
                'tmdb_id' => $person['tmdb_id'],
                'age' => $age,
                'department' => $details['known_for_department'] ?? '',
                'photo' => $details['profile_path'] ?? null,
            ];
        }

        $postTitle = 'Bugün Doğan Oyuncu ve Yönetmenler: ' . now()->format('d.m.Y');
        $excerpt = implode(', ', array_column($people, 'name')) . ' ve daha fazlası bugün doğdu!';

        $body = "# Bugün Doğan Sinema Dünyasının Yıldızları\n\n";
        $body .= now()->format('d.m.Y') . " tarihinde doğan sinema dünyasının ünlü isimleri:\n\n";

        foreach ($people as $p) {
            $dept = $p['department'] === 'Acting' ? 'Oyuncu' : ($p['department'] === 'Directing' ? 'Yönetmen' : 'Sinema Sanatçısı');
            $body .= "**[{$p['name']}](/kisi/{$p['tmdb_id']}-" . \Illuminate\Support\Str::slug($p['name']) . ")** — {$dept}, {$p['age']} yaşında\n\n";
        }

        $body .= "Bu isimlerin filmografilerine göz atmak için profillerine tıklayın! 🎂\n\n";
        $body .= "Her gün güncellenen doğum günleri için [ana sayfamızı](/) ziyaret edin.";

        $imageUrl = !empty($people[0]['photo']) ? 'https://image.tmdb.org/t/p/w780' . $people[0]['photo'] : null;

        return Post::create([
            'title' => $postTitle, 'category' => 'Haber', 'excerpt' => $excerpt,
            'body' => $body, 'image_url' => $imageUrl, 'read_time' => 4,
            'is_published' => true, 'published_at' => now(),
        ]);
    }

    private function generateTrendingPost(): ?Post
    {
        $trending = $this->tmdb->getTrending('week');
        $trending = array_slice($trending, 0, 10);
        if (empty($trending)) return null;

        $postTitle = 'Bu Haftanın En Popüler Filmleri: ' . now()->format('d.m.Y');
        $first = $trending[0];
        $excerpt = "\"{$first['title']}\" bu hafta zirvede! İşte haftanın en çok konuşulan 10 filmi.";

        $body = "# Bu Haftanın En Popüler Filmleri\n\n";
        $body .= "TMDB verilerine göre " . now()->format('d.m.Y') . " haftasının en popüler 10 filmi:\n\n";

        foreach ($trending as $i => $movie) {
            $num = $i + 1;
            $mTitle = $movie['title'] ?? $movie['name'] ?? '';
            $slug = \Illuminate\Support\Str::slug($mTitle);
            $body .= "{$num}. **[{$mTitle}](/film/{$movie['id']}-{$slug})** — ★ " . number_format($movie['vote_average'] ?? 0, 1) . "\n\n";
        }

        $body .= "Bu filmleri puanlamak ve listene eklemek için film sayfalarını ziyaret et! 🍿\n\n";
        $body .= "Daha fazlası için [vizyonda](/vizyonda) ve [keşfet](/kesfet) sayfalarımıza bakabilirsin.";

        $imageUrl = ($trending[0]['poster_path'] ?? null) ? 'https://image.tmdb.org/t/p/w780' . $trending[0]['poster_path'] : null;

        return Post::create([
            'title' => $postTitle, 'category' => 'Liste', 'excerpt' => $excerpt,
            'body' => $body, 'image_url' => $imageUrl, 'read_time' => 5,
            'is_published' => true, 'published_at' => now(),
        ]);
    }

    private function generateAnniversaryPost(): ?Post
    {
        $today = now();
        $month = $today->format('m');
        $day = $today->format('d');

        $allMovies = [];
        for ($year = 2024; $year >= 2000; $year--) {
            $results = $this->tmdb->discoverMovies([
                'primary_release_date.gte' => "{$year}-{$month}-{$day}",
                'primary_release_date.lte' => "{$year}-{$month}-{$day}",
                'sort_by' => 'vote_count.desc',
            ]);
            $allMovies = array_merge($allMovies, $results);
            if (count($allMovies) >= 10) break;
        }

        usort($allMovies, fn($a, $b) => ($b['vote_count'] ?? 0) <=> ($a['vote_count'] ?? 0));
        $movies = array_slice($allMovies, 0, 10);
        if (empty($movies)) return null;

        $postTitle = 'Bu Tarihte Vizyona Giren Efsane Filmler: ' . $today->format('d.m.Y');
        $excerpt = "Sinema tarihinde bugün ({$today->format('d.m.Y')}) vizyona giren en iyi filmleri listeledik.";

        $body = "# Bu Tarihte Vizyona Giren Filmler\n\n";
        $body .= "Sinema tarihinde {$today->format('d.m.Y')} tarihinde vizyona giren en popüler filmler:\n\n";

        foreach ($movies as $movie) {
            $mTitle = $movie['title'] ?? '';
            $year = substr($movie['release_date'] ?? '—', 0, 4);
            $slug = \Illuminate\Support\Str::slug($mTitle);
            $body .= "**[{$mTitle}](/film/{$movie['id']}-{$slug})** ({$year}) — ★ " . number_format($movie['vote_average'] ?? 0, 1) . " ({$movie['vote_count']} oy)\n\n";
        }

        $body .= "Bu filmleri izlemek için film sayfalarından platform bilgisine bakabilirsin! 🎬";

        $imageUrl = ($movies[0]['poster_path'] ?? null) ? 'https://image.tmdb.org/t/p/w780' . $movies[0]['poster_path'] : null;

        return Post::create([
            'title' => $postTitle, 'category' => 'Liste', 'excerpt' => $excerpt,
            'body' => $body, 'image_url' => $imageUrl, 'read_time' => 5,
            'is_published' => true, 'published_at' => now(),
        ]);
    }

    private function generateUpcomingPost(): ?Post
    {
        $upcoming = $this->tmdb->getUpcoming();
        $upcoming = array_slice($upcoming, 0, 10);
        if (empty($upcoming)) return null;

        $postTitle = 'Yakında Vizyonda: ' . now()->format('F Y') . ' Ayının En İyi Filmleri';
        $excerpt = 'Önümüzdeki haftalarda sinemalarda olacak en heyecan verici yapımları sizin için listeledik.';

        $body = "# Yakında Vizyonda\n\n";
        $body .= "Yakında sinemalarda olacak en çok beklenen filmler:\n\n";

        foreach ($upcoming as $movie) {
            $mTitle = $movie['title'] ?? '';
            $date = $movie['release_date'] ?? 'TBA';
            $slug = \Illuminate\Support\Str::slug($mTitle);
            $body .= "**[{$mTitle}](/film/{$movie['id']}-{$slug})** — Vizyon: " . ($date !== 'TBA' ? \Carbon\Carbon::parse($date)->format('d.m.Y') : 'TBA') . "\n\n";
        }

        $body .= "Takviminizi ayarlayın! Daha fazlası için [yakında](/yakinda) sayfamızı ziyaret edin. 🎬";

        $imageUrl = ($upcoming[0]['poster_path'] ?? null) ? 'https://image.tmdb.org/t/p/w780' . $upcoming[0]['poster_path'] : null;

        return Post::create([
            'title' => $postTitle, 'category' => 'Haber', 'excerpt' => $excerpt,
            'body' => $body, 'image_url' => $imageUrl, 'read_time' => 5,
            'is_published' => true, 'published_at' => now(),
        ]);
    }

    private function generateRecommendationPost(): ?Post
    {
        $moods = config('moods');
        $moodSlug = array_rand($moods);
        $mood = $moods[$moodSlug];

        $movies = $this->tmdb->discoverByGenres($mood['genres'], 1, 'vote_average.desc');
        $movies = array_slice($movies, 0, 8);
        if (empty($movies)) return null;

        $postTitle = "{$mood['emoji']} {$mood['label']} Ruh Halinde İzlenecek En İyi Filmler";
        $excerpt = "{$mood['label']} modundaysanız tam size göre bir liste hazırladık.";

        $body = "# {$mood['emoji']} {$mood['label']} Ruh Halinde İzlenecek Filmler\n\n";
        $body .= "{$mood['description']} bir şeyler izlemek istiyorsanız, işte size özel seçtiğimiz filmler:\n\n";

        foreach ($movies as $movie) {
            $mTitle = $movie['title'] ?? '';
            $slug = \Illuminate\Support\Str::slug($mTitle);
            $body .= "**[{$mTitle}](/film/{$movie['id']}-{$slug})** — ★ " . number_format($movie['vote_average'] ?? 0, 1) . "\n";
            if (!empty($movie['overview'])) {
                $body .= \Illuminate\Support\Str::limit($movie['overview'], 150) . "\n";
            }
            $body .= "\n";
        }

        $body .= "Daha fazla {$mood['label']} filmi için [/{$moodSlug}](/mod/{$moodSlug}) sayfamıza göz atın! 🎬";

        $imageUrl = ($movies[0]['poster_path'] ?? null) ? 'https://image.tmdb.org/t/p/w780' . $movies[0]['poster_path'] : null;

        return Post::create([
            'title' => $postTitle, 'category' => 'Liste', 'excerpt' => $excerpt,
            'body' => $body, 'image_url' => $imageUrl, 'read_time' => 6,
            'is_published' => true, 'published_at' => now(),
        ]);
    }
}
