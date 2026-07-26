<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

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
        if ($ratedCount < 2) {
            return $this->getPopularFallback($limit);
        }

        $cacheKey = 'recommendations:user:' . $user->id;
        return Cache::remember($cacheKey, 1800, function () use ($user, $limit) {
            return $this->generate($user, $limit);
        });
    }

    private function generate(User $user, int $limit): array
    {
        $allItems = [];

        $topRated = $user->ratings()
            ->with('movie')
            ->where('rating', '>=', 7)
            ->latest()
            ->take(5)
            ->get();

        foreach ($topRated as $rating) {
            $tmdbId = $rating->movie->tmdb_id ?? null;
            if (!$tmdbId) continue;

            $similar = $this->tmdb->getMovieRecommendations($tmdbId);
            $allItems = array_merge($allItems, array_slice($similar, 0, 5));
        }

        if (count($allItems) < $limit) {
            $favoriteGenreIds = $user->favoriteGenres()->pluck('genres.tmdb_id')->toArray();
            if (empty($favoriteGenreIds)) {
                $genreIds = $this->getTopRatedGenreIds($user);
            } else {
                $genreIds = $favoriteGenreIds;
            }

            if (!empty($genreIds)) {
                $genreMovies = $this->tmdb->discoverByGenres(array_slice($genreIds, 0, 3), rand(1, 5));
                $allItems = array_merge($allItems, $genreMovies);
            }
        }

        if (count($allItems) < $limit) {
            $allItems = array_merge($allItems, $this->tmdb->getPopularMovies(rand(1, 3)));
        }

        $ratedTmdbIds = $user->ratings()
            ->whereHas('movie')
            ->with('movie')
            ->get()
            ->pluck('movie.tmdb_id')
            ->filter()
            ->toArray();

        $seen = [];
        $unique = [];
        foreach ($allItems as $item) {
            $id = $item['id'] ?? 0;
            if ($id && !in_array($id, $seen) && !in_array($id, $ratedTmdbIds)) {
                $seen[] = $id;
                $unique[] = $item;
            }
            if (count($unique) >= $limit) break;
        }

        shuffle($unique);
        return $unique;
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
        return Cache::remember('popular_fallback', 3600, function () use ($limit) {
            return $this->tmdb->getPopularMovies(rand(1, 3));
        });
    }

    public function getForMovie(int $tmdbId, int $limit = 8): array
    {
        return array_slice($this->tmdb->getMovieRecommendations($tmdbId), 0, $limit);
    }

    public function getTrendingInGenre(array $genreIds, int $limit = 8): array
    {
        return array_slice($this->tmdb->discoverByGenres($genreIds), 0, $limit);
    }
}
