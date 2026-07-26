<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    private string $baseUrl = 'https://api.themoviedb.org/3';
    private string $apiKey;
    private string $language = 'tr-TR';

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
    }

    private function fetch(string $endpoint, array $params = []): ?array
    {
        $params['api_key'] = $this->apiKey;
        $params['language'] = $this->language;

        $cacheKey = 'tmdb:' . $endpoint . ':' . md5(serialize($params));

        return Cache::remember($cacheKey, 3600, function () use ($endpoint, $params) {
            $response = Http::timeout(10)
                ->retry(2, 500)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });
    }

    public function getImageUrl(?string $path, string $size = 'w500'): ?string
    {
        if (!$path) return null;
        return 'https://image.tmdb.org/t/p/' . $size . $path;
    }

    public function getGenres(): array
    {
        $data = $this->fetch('/genre/movie/list');
        return $data['genres'] ?? [];
    }

    public function discoverMovies(array $filters = []): array
    {
        $params = array_merge([
            'sort_by' => 'popularity.desc',
            'page' => $filters['page'] ?? 1,
            'vote_count.gte' => 50,
        ], $filters);

        $data = $this->fetch('/discover/movie', $params);
        return $data['results'] ?? [];
    }

    public function discoverByGenres(array $genreIds, int $page = 1, string $sortBy = 'vote_average.desc'): array
    {
        return $this->discoverMovies([
            'with_genres' => implode(',', $genreIds),
            'sort_by' => $sortBy,
            'page' => $page,
        ]);
    }

    public function getMovieDetails(int $movieId): ?array
    {
        return $this->fetch('/movie/' . $movieId, [
            'append_to_response' => 'videos,credits,recommendations',
        ]);
    }

    public function getMovieCredits(int $movieId): ?array
    {
        return $this->fetch('/movie/' . $movieId . '/credits');
    }

    public function getMovieVideos(int $movieId): array
    {
        $data = $this->fetch('/movie/' . $movieId . '/videos');
        return $data['results'] ?? [];
    }

    public function getPopularMovies(int $page = 1): array
    {
        $data = $this->fetch('/movie/popular', ['page' => $page]);
        return $data['results'] ?? [];
    }

    public function getTrending(string $timeWindow = 'week'): array
    {
        $data = $this->fetch('/trending/movie/' . $timeWindow);
        return $data['results'] ?? [];
    }

    public function getUpcoming(int $page = 1): array
    {
        $data = $this->fetch('/discover/movie', [
            'primary_release_date.gte' => now()->format('Y-m-d'),
            'sort_by' => 'primary_release_date.asc',
            'page' => $page,
        ]);
        return $data['results'] ?? [];
    }

    public function getNowPlaying(int $page = 1): array
    {
        $data = $this->fetch('/movie/now_playing', ['page' => $page]);
        return $data['results'] ?? [];
    }

    public function searchMovies(string $query, int $page = 1): array
    {
        $data = $this->fetch('/search/movie', ['query' => $query, 'page' => $page]);
        return $data['results'] ?? [];
    }

    public function searchMulti(string $query, int $page = 1): array
    {
        $data = $this->fetch('/search/multi', ['query' => $query, 'page' => $page]);
        return $data['results'] ?? [];
    }

    public function getPersonDetails(int $personId): ?array
    {
        return $this->fetch('/person/' . $personId, [
            'append_to_response' => 'movie_credits,tv_credits,external_ids',
        ]);
    }

    public function getMovieRecommendations(int $movieId): array
    {
        $data = $this->fetch('/movie/' . $movieId . '/recommendations');
        return $data['results'] ?? [];
    }

    public function getWatchProviders(int $movieId): array
    {
        $data = $this->fetch('/movie/' . $movieId . '/watch/providers');
        if (!$data) return [];

        $results = $data['results'] ?? [];

        $tr = $results['TR'] ?? null;
        $us = $results['US'] ?? null;

        return [
            'TR' => [
                'stream' => $tr['flatrate'] ?? [],
                'rent' => $tr['rent'] ?? [],
                'buy' => $tr['buy'] ?? [],
            ],
            'US' => [
                'stream' => $us['flatrate'] ?? [],
                'rent' => $us['rent'] ?? [],
                'buy' => $us['buy'] ?? [],
            ],
        ];
    }

    public function getTrailerUrl(?array $videos): ?string
    {
        if (empty($videos)) return null;

        foreach ($videos as $video) {
            if ($video['site'] === 'YouTube' && $video['type'] === 'Trailer') {
                return 'https://www.youtube.com/embed/' . $video['key'];
            }
        }

        foreach ($videos as $video) {
            if ($video['site'] === 'YouTube') {
                return 'https://www.youtube.com/embed/' . $video['key'];
            }
        }

        return null;
    }

    // ─── TV Series ─────────────────────────────────

    public function getTVDetails(int $seriesId): ?array
    {
        return $this->fetch('/tv/' . $seriesId, [
            'append_to_response' => 'videos,credits,recommendations,watch/providers',
        ]);
    }

    public function getTVSeasonDetails(int $seriesId, int $seasonNumber): ?array
    {
        return $this->fetch("/tv/{$seriesId}/season/{$seasonNumber}", [
            'append_to_response' => 'credits',
        ]);
    }

    public function getTVCredits(int $seriesId): ?array
    {
        return $this->fetch('/tv/' . $seriesId . '/credits');
    }

    public function getTVVideos(int $seriesId): array
    {
        $data = $this->fetch('/tv/' . $seriesId . '/videos');
        return $data['results'] ?? [];
    }

    public function getPopularTV(int $page = 1): array
    {
        $data = $this->fetch('/tv/popular', ['page' => $page]);
        return $data['results'] ?? [];
    }

    public function getTrendingTV(string $timeWindow = 'week'): array
    {
        $data = $this->fetch('/trending/tv/' . $timeWindow);
        return $data['results'] ?? [];
    }

    public function getAiringTodayTV(int $page = 1): array
    {
        $data = $this->fetch('/tv/airing_today', ['page' => $page]);
        return $data['results'] ?? [];
    }

    public function discoverTV(array $filters = []): array
    {
        $params = array_merge([
            'sort_by' => 'popularity.desc',
            'page' => $filters['page'] ?? 1,
        ], $filters);

        $data = $this->fetch('/discover/tv', $params);
        return $data['results'] ?? [];
    }

    public function discoverTVByGenres(array $genreIds, int $page = 1, string $sortBy = 'vote_average.desc'): array
    {
        return $this->discoverTV([
            'with_genres' => implode(',', $genreIds),
            'sort_by' => $sortBy,
            'page' => $page,
        ]);
    }

    public function searchTV(string $query, int $page = 1): array
    {
        $data = $this->fetch('/search/tv', ['query' => $query, 'page' => $page]);
        return $data['results'] ?? [];
    }

    public function getTVWatchProviders(int $seriesId): array
    {
        $data = $this->fetch('/tv/' . $seriesId . '/watch/providers');
        if (!$data) return [];

        $tr = $data['results']['TR'] ?? null;
        return [
            'TR' => [
                'stream' => $tr['flatrate'] ?? [],
                'rent' => $tr['rent'] ?? [],
                'buy' => $tr['buy'] ?? [],
            ],
        ];
    }

    // ─── Existing ───────────────────────────────────

    public function getProviderMovies(int $providerId, string $region = 'TR', int $page = 1): array
    {
        $data = $this->fetch('/discover/movie', [
            'with_watch_providers' => $providerId,
            'watch_region' => $region,
            'sort_by' => 'popularity.desc',
            'page' => $page,
        ]);
        return $data['results'] ?? [];
    }

    public function getCompanyDetails(int $companyId): ?array
    {
        return $this->fetch('/company/' . $companyId);
    }

    public function getCompanyMovies(int $companyId, int $page = 1): array
    {
        $data = $this->fetch('/discover/movie', [
            'with_companies' => $companyId,
            'sort_by' => 'popularity.desc',
            'page' => $page,
        ]);
        return $data['results'] ?? [];
    }

    public function discoverByCountry(string $countryCode, int $page = 1): array
    {
        $data = $this->fetch('/discover/movie', [
            'with_origin_country' => $countryCode,
            'sort_by' => 'popularity.desc',
            'page' => $page,
        ]);
        return $data['results'] ?? [];
    }
}
