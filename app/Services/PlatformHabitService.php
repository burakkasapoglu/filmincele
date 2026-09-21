<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PlatformHabitService
{
    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    /**
     * Kullanicinin platform aliskanliklari:
     * izleme listesi + puanlardaki filmlerin TR platform dagilimi.
     * Ornek: ['8' => ['name' => 'Netflix', 'count' => 9], ...]
     */
    public function getHabits(User $user, int $limit = 5): array
    {
        return Cache::remember('platform-habits:' . $user->id, 3600, function () use ($user, $limit) {
            $movies = collect();

            $watchlist = $user->watchlists()
                ->with(['movies' => function ($q) { $q->latest('watchlist_movie.added_at')->take(40); }])
                ->get()
                ->pluck('movies')
                ->flatten()
                ->unique('id');

            $rated = $user->ratings()
                ->with('movie')
                ->latest()
                ->take(40)
                ->get()
                ->pluck('movie')
                ->filter();

            $movies = $watchlist->merge($rated)->unique('id');

            $counts = [];
            foreach ($movies as $movie) {
                if (!$movie || !($movie->tmdb_id ?? null)) continue;

                $tmdbId = $movie->tmdb_id;

                $providers = Cache::remember('tr-providers:' . $tmdbId, 86400, function () use ($tmdbId) {
                    return $this->tmdb->getWatchProviders($tmdbId);
                });

                $tr = $providers['TR'] ?? [];
                $names = array_merge(
                    array_column($tr['stream'] ?? [], 'provider_name'),
                    array_column($tr['rent'] ?? [], 'provider_name'),
                    array_column($tr['buy'] ?? [], 'provider_name')
                );

                foreach (array_unique($names) as $name) {
                    $counts[$name] = ($counts[$name] ?? 0) + 1;
                }
            }

            arsort($counts);

            $out = [];
            foreach (array_slice($counts, 0, $limit, true) as $name => $count) {
                $out[] = ['name' => $name, 'count' => $count];
            }

            return $out;
        });
    }

    /**
     * Kullanicinin en cekici platformu (aliskanlik uzerinden).
     */
    public function getTopPlatform(User $user): ?array
    {
        $habits = $this->getHabits($user, 1);
        return $habits[0] ?? null;
    }
}
