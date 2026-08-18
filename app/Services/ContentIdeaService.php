<?php

namespace App\Services;

use App\Models\ContentIdea;
use App\Models\Post;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ContentIdeaService
{
    public function getUpcoming(int $days = 21): Collection
    {
        return Cache::remember('content-ideas:upcoming:' . now()->toDateString(), 1800, function () use ($days) {
            $ideas = collect()
                ->merge($this->birthdayIdeas($days))
                ->merge($this->anniversaryIdeas($days))
                ->merge($this->triviaIdeas())
                ->merge($this->trendIdeas());

            $seen = [];
            return $ideas
                ->filter(function ($idea) use (&$seen) {
                    $key = $idea['type'] . ':' . $idea['tmdb_ref'];
                    if (isset($seen[$key])) return false;
                    $seen[$key] = true;
                    return true;
                })
                ->sortBy(fn ($i) => $i['event_date'] ?? now()->addYears(10))
                ->values();
        });
    }

    private function birthdayIdeas(int $days): array
    {
        $start = now();
        $ideas = [];

        foreach (range(0, $days) as $offset) {
            $date = $start->copy()->addDays($offset);
            $monthDay = $date->format('m-d');

            $people = collect(config('cinema-calendar'))
                ->filter(fn ($p) => substr($p['birthday'], 5) === $monthDay);

            foreach ($people as $p) {
                $age = null;
                if (!str_contains($p['birthday'], '0000')) {
                    $age = \Carbon\Carbon::parse($p['birthday'])->age + ($offset > 0 ? 1 : 0);
                }

                $when = $date->format('d.m.Y') . ' (' . $this->relativeLabel($date) . ')';
                $ideas[] = [
                    'type' => 'birthday',
                    'icon' => '🎂',
                    'title' => $p['name'] . ' doğum günü' . ($age ? " ({$age})" : ''),
                    'event_date' => $date->toDateString(),
                    'tmdb_ref' => 'person:' . $p['tmdb_id'],
                    'tmdb_id' => $p['tmdb_id'],
                    'kind' => 'person',
                    'suggestion' => "{$p['name']}" . ($age ? " {$age}. yaşına giriyor. " : '') .
                        "Kariyerinin dönüm noktaları, unutulmaz rolleri ve az bilinen yönleriyle bir içerik hazırlanabilir. " .
                        "Video fikri: '5 dakikada {$p['name']}' veya en iyi 10 performansı listelemek.",
                    'when_label' => $when,
                ];
            }
        }

        return $ideas;
    }

    private function anniversaryIdeas(int $days): array
    {
        $tmdb = app(TmdbService::class);
        $ideas = [];

        foreach ([5, 10, 15, 20, 25] as $offsetDays) {
            $date = now()->addDays($offsetDays);
            $month = $date->format('m');
            $day = $date->format('d');

            $movies = $tmdb->discoverMovies([
                'primary_release_date.gte' => $date->format('Y-m-d'),
                'primary_release_date.lte' => $date->format('Y-m-d'),
                'sort_by' => 'vote_count.desc',
            ]);

            foreach (array_slice($movies, 0, 3) as $m) {
                $year = substr($m['release_date'] ?? '', 0, 4);
                if (!$year) continue;

                $rounded = (now()->year - (int) $year) % 5 === 0;
                $ideas[] = [
                    'type' => 'anniversary',
                    'icon' => $rounded ? '🏆' : '🎬',
                    'title' => ($m['title'] ?? '') . " vizyona girişinin " . (now()->year - (int) $year) . ". yılı",
                    'event_date' => $date->toDateString(),
                    'tmdb_ref' => 'movie:' . $m['id'],
                    'tmdb_id' => $m['id'],
                    'kind' => 'movie',
                    'suggestion' => "**{$m['title']}** ({$year}) tam " . (now()->year - (int) $year) . " yıl önce vizyona girdi. " .
                        "Film bugün kült statüsünde mi? Gişesi, ödülleri ve mirası üzerine bir retrospect yazısı veya " .
                        "'Biliyor muydunuz?' formatında anakronik detaylar paylaşılabilir.",
                    'when_label' => $date->format('d.m.Y') . ' (' . $this->relativeLabel($date) . ')',
                ];
            }
        }

        return $ideas;
    }

    private function triviaIdeas(): array
    {
        return [
            [
                'type' => 'trivia',
                'icon' => '💡',
                'title' => 'Biliyor muydunuz? — Yönetmenlerin gizli Cameo rolları',
                'event_date' => null,
                'tmdb_ref' => 'trivia:cameo',
                'suggestion' => 'Ünlü yönetmenlerin kendi filmlerindeki küçük rolleri (Hitchcock, Tarantino, Nolan...) bir araya getirilerek "Bunu biliyor muydunuz?" formatında video veya liste içeriği yapılabilir.',
                'when_label' => 'Esnek',
            ],
            [
                'type' => 'trivia',
                'icon' => '💡',
                'title' => 'Biliyor muydunuz? — Aynı filmi çeviren iki yönetmen',
                'event_date' => null,
                'tmdb_ref' => 'trivia:remakes',
                'suggestion' => 'Birbirinden farklı iki yönetmenin aynı hikâyeyi farklı yorumladığı filmler (örn. iki farklı versiyon/uyarlama) karşılaştırmalı içerik olabilir.',
                'when_label' => 'Esnek',
            ],
            [
                'type' => 'trivia',
                'icon' => '🍿',
                'title' => 'İzleyici anketi — Bu ay çıkacak filmlerden hangisini bekliyorsunuz?',
                'event_date' => null,
                'tmdb_ref' => 'trivia:poll',
                'suggestion' => 'Yakında çıkacak 5-6 film listelenip takipçilere soru sorulabilir; sonuçlar bir sonraki yazıda değerlendirilir. Etkileşim artırır.',
                'when_label' => 'Esnek',
            ],
        ];
    }

    private function trendIdeas(): array
    {
        $tmdb = app(TmdbService::class);
        $trending = collect($tmdb->getTrending('week'))->take(3);
        $ideas = [];

        foreach ($trending as $i => $t) {
            $title = $t['title'] ?? $t['name'] ?? '';
            if (!$title) continue;
            $ideas[] = [
                'type' => 'trend',
                'icon' => '🔥',
                'title' => 'Trend: ' . $title . ' neden konuşuluyor?',
                'event_date' => null,
                'tmdb_ref' => ($t['media_type'] ?? 'movie') . ':' . $t['id'],
                'tmdb_id' => $t['id'],
                'kind' => $t['media_type'] === 'tv' ? 'tv' : 'movie',
                'suggestion' => "**{$title}** bu hafta trend listesinde. Neden popüler olduğu, benzer yapımlar ve izleme rehberi içeren güncel bir içerik hızlı trafik çeker.",
                'when_label' => 'Bu hafta',
            ];
        }

        return $ideas;
    }

    private function relativeLabel(CarbonInterface $date): string
    {
        $diff = today()->diffInDays($date->copy()->startOfDay());
        return match (true) {
            $diff === 0 => 'bugün',
            $diff === 1 => 'yarın',
            $diff <= 6 => $date->locale('tr')->dayName,
            default => $diff . ' gün sonra',
        };
    }

    public function statusFor(array $idea, Collection $existing): string
    {
        $row = $existing->first(fn ($e) => $e->tmdb_ref === $idea['tmdb_ref'] && $e->type === $idea['type']);
        return $row?->status ?? 'new';
    }
}
