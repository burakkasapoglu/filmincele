<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class RecommendationService
{
    private TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function getForUser(User $user, int $limit = 12): array
    {
        $ratedCount = $user->ratings()->count();
        $listCount = $user->watchlists()->withCount('movies')->get()->sum('movies_count');

        if ($ratedCount < 1 && $listCount < 1) {
            return $this->getPopularFallback($limit);
        }

        $cacheKey = 'recommendations:user:' . $user->id;
        return Cache::remember($cacheKey, 1800, function () use ($user, $limit) {
            return $this->generate($user, $limit);
        });
    }

    /**
     * Film Turkiye'de izlenebilir mi? (platform/izle/kiralama/satinalma)
     */
    private function isWatchableInTurkey(array $movie): bool
    {
        $tmdb = $this->tmdb;
        $id = (int) ($movie['id'] ?? 0);
        if (!$id) return false;

        $providers = Cache::remember('tr-providers:' . $id, 86400, function () use ($tmdb, $id) {
            return $tmdb->getWatchProviders($id);
        });

        $tr = $providers['TR'] ?? [];
        return !empty($tr['stream']) || !empty($tr['rent']) || !empty($tr['buy']);
    }

    /**
     * Listeyi TR'de izlenebilir olanlara gore filtrele; yetmezse discover ile doldur.
     */
    private function filterWatchable(array $movies, int $limit): array
    {
        $watchable = [];
        foreach ($movies as $m) {
            if (count($watchable) >= $limit) break;
            if ($this->isWatchableInTurkey($m)) {
                $watchable[] = $m;
            }
        }

        if (count($watchable) < $limit) {
            // TR'de izlenebilen populer filmlerle doldur
            $fillable = $this->tmdb->discoverMovies([
                'with_watch_providers' => '8|119|337|1899|2|3|188|11|342',
                'watch_region' => 'TR',
                'sort_by' => 'popularity.desc',
                'page' => 1,
            ]);

            $seen = array_column($watchable, 'id');
            foreach ($fillable as $m) {
                if (count($watchable) >= $limit) break;
                if (!in_array($m['id'], $seen)) {
                    $m['_reason'] = $m['_reason'] ?? 'Türkiye\'de izlenebilir';
                    $m['_weight'] = $m['_weight'] ?? 1;
                    $watchable[] = $m;
                    $seen[] = $m['id'];
                }
            }
        }

        return array_slice($watchable, 0, $limit);
    }

    private function generate(User $user, int $limit): array
    {
        $allItems = collect();
        $reasons = [];

        $ratedTmdbIds = $this->getRatedTmdbIds($user);
        $watchlistTmdbIds = $this->getWatchlistTmdbIds($user);

        // 1. High-rated movies → TMDB similar (highest weight, most relevant)
        $topRated = $user->ratings()
            ->with('movie')
            ->where('rating', '>=', 7)
            ->latest()
            ->take(5)
            ->get();

        foreach ($topRated as $rating) {
            $tmdbId = $rating->movie->tmdb_id ?? null;
            $title = $rating->movie->title ?? '';
            if (!$tmdbId) continue;

            $similar = $this->tmdb->getMovieRecommendations($tmdbId);
            foreach (array_slice($similar, 0, 4) as $m) {
                $m['_reason'] = "Çünkü \"{$title}\" filmini puanladın";
                $m['_weight'] = 10;
                $allItems->push($m);
            }
        }

        // 2. Recently watchlisted → TMDB similar
        $watchlistMovies = $user->watchlists()
            ->with(['movies' => function($q) { $q->latest('watchlist_movie.added_at')->take(3); }])
            ->get()
            ->pluck('movies')
            ->flatten()
            ->unique('id')
            ->take(3);

        foreach ($watchlistMovies as $movie) {
            $similar = $this->tmdb->getMovieRecommendations($movie->tmdb_id);
            foreach (array_slice($similar, 0, 3) as $m) {
                $m['_reason'] = "Çünkü \"{$movie->title}\" listende var";
                $m['_weight'] = 7;
                $allItems->push($m);
            }
        }

        // 3. Favorite genres → discover popular
        $favoriteGenreIds = $user->favoriteGenres()->pluck('tmdb_id')->toArray();
        if (empty($favoriteGenreIds)) {
            $favoriteGenreIds = $this->getTopRatedGenreIds($user);
        }

        if (!empty($favoriteGenreIds)) {
            $genreMovies = $this->tmdb->discoverByGenres(array_slice($favoriteGenreIds, 0, 3), rand(1, 4), 'popularity.desc');
            foreach ($genreMovies as $m) {
                $m['_reason'] = 'Sevdiğin türlerden bir seçki';
                $m['_weight'] = 3;
                $allItems->push($m);
            }
        }

        // 4. Pad with popular if not enough
        if ($allItems->count() < $limit) {
            $popular = collect($this->tmdb->getPopularMovies(rand(1, 4)));
            foreach ($popular as $m) {
                $m['_reason'] = 'Popüler öneri';
                $m['_weight'] = 1;
                $allItems->push($m);
            }
        }

        // Deduplicate, exclude already engaged content, sort by weight
        $excludeIds = array_merge($ratedTmdbIds, $watchlistTmdbIds);
        $seen = [];
        $unique = $allItems
            ->filter(function ($item) use (&$seen, $excludeIds) {
                $id = $item['id'] ?? 0;
                if (!$id || in_array($id, $seen) || in_array($id, $excludeIds)) return false;
                $seen[] = $id;
                return true;
            })
            ->sortByDesc('_weight')
            ->take($limit * 3)
            ->values()
            ->toArray();

        // Sadece Turkiye'de izlenebilir olanlar
        return $this->filterWatchable($unique, $limit);
    }

    private function getRatedTmdbIds(User $user): array
    {
        return $user->ratings()
            ->whereHas('movie')
            ->with('movie')
            ->get()
            ->pluck('movie.tmdb_id')
            ->filter()
            ->values()
            ->toArray();
    }

    private function getWatchlistTmdbIds(User $user): array
    {
        return $user->watchlists()
            ->with('movies')
            ->get()
            ->pluck('movies')
            ->flatten()
            ->pluck('tmdb_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    private function getTopRatedGenreIds(User $user): array
    {
        return $user->ratings()
            ->where('rating', '>=', 7)
            ->with('movie.genres')
            ->get()
            ->pluck('movie.genres')
            ->flatten()
            ->pluck('tmdb_id')
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(3)
            ->toArray();
    }

    private function getPopularFallback(int $limit): array
    {
        return Cache::remember('popular_fallback_tr', 3600, function () use ($limit) {
            $movies = $this->tmdb->discoverMovies([
                'with_watch_providers' => '8|119|337|1899|2|3|188|11|342',
                'watch_region' => 'TR',
                'sort_by' => 'popularity.desc',
                'page' => rand(1, 3),
            ]);

            foreach ($movies as &$m) {
                $m['_reason'] = 'Popüler öneri';
                $m['_weight'] = 1;
            }

            return array_slice($movies, 0, $limit);
        });
    }
}
