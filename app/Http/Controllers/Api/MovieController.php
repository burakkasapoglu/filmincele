<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    /**
     * Search movies from TMDB
     * GET /api/movies/search?query=inception&page=1
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $page = (int) $request->get('page', 1);

        if (empty($query)) {
            return $this->popular($request);
        }

        $results = $this->tmdb->searchMovies($query, $page);

        $movies = collect($results)->map(fn($m) => [
            'id' => $m['id'],
            'title' => $m['title'] ?? '',
            'overview' => $m['overview'] ?? '',
            'posterUrl' => $m['poster_path'] ? "https://image.tmdb.org/t/p/w500{$m['poster_path']}" : null,
            'releaseDate' => $m['release_date'] ?? '',
            'voteAverage' => $m['vote_average'] ?? 0,
        ])->values();

        return response()->json([
            'movies' => $movies,
            'page' => $page,
            'total' => count($movies),
        ]);
    }

    /**
     * Get movie details
     * GET /api/movies/{id}
     */
    public function detail(int $id): JsonResponse
    {
        $movie = $this->tmdb->getMovieDetails($id);
        if (!$movie) {
            return response()->json(['error' => 'Film bulunamadı'], 404);
        }

        $credits = $this->tmdb->getMovieCredits($id);
        $videos = $this->tmdb->getMovieVideos($id);
        $director = collect($credits['crew'] ?? [])->firstWhere('job', 'Director');

        return response()->json([
            'id' => $movie['id'],
            'title' => $movie['title'] ?? '',
            'overview' => $movie['overview'] ?? '',
            'posterUrl' => $movie['poster_path'] ? "https://image.tmdb.org/t/p/w500{$movie['poster_path']}" : null,
            'backdropUrl' => $movie['backdrop_path'] ? "https://image.tmdb.org/t/p/w1280{$movie['backdrop_path']}" : null,
            'releaseDate' => $movie['release_date'] ?? '',
            'voteAverage' => $movie['vote_average'] ?? 0,
            'voteCount' => $movie['vote_count'] ?? 0,
            'runtime' => $movie['runtime'] ?? null,
            'genres' => $movie['genres'] ?? [],
            'director' => $director ? ['id' => $director['id'], 'name' => $director['name']] : null,
            'cast' => collect($credits['cast'] ?? [])->take(10)->map(fn($c) => [
                'id' => $c['id'], 'name' => $c['name'], 'character' => $c['character'] ?? null,
            ])->values(),
            'trailers' => collect($videos ?? [])->where('type', 'Trailer')->take(3)->map(fn($v) => [
                'key' => $v['key'], 'name' => $v['name'], 'site' => $v['site'],
            ])->values(),
        ]);
    }

    /**
     * Popular movies
     * GET /api/movies?page=1
     */
    public function popular(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $results = $this->tmdb->getPopularMovies($page);

        $movies = collect($results)->map(fn($m) => [
            'id' => $m['id'],
            'title' => $m['title'] ?? '',
            'overview' => $m['overview'] ?? '',
            'posterUrl' => $m['poster_path'] ? "https://image.tmdb.org/t/p/w500{$m['poster_path']}" : null,
            'releaseDate' => $m['release_date'] ?? '',
            'voteAverage' => $m['vote_average'] ?? 0,
        ])->values();

        return response()->json([
            'movies' => $movies,
            'page' => $page,
            'total' => count($movies),
        ]);
    }
}
