<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Rating;
use App\Models\Watchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WatchedController extends Controller
{
    /**
     * Get user's watched movies
     * GET /api/watched
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $watched = Watchlist::where('user_id', $user->id)
            ->where('name', 'İzlediklerim')
            ->with('movies')
            ->first();

        $movies = $watched ? $watched->movies->map(function ($movie) use ($user) {
            $rating = Rating::where('user_id', $user->id)
                ->where('movie_id', $movie->id)
                ->first();

            return [
                'id' => $movie->id,
                'tmdbId' => $movie->tmdb_id,
                'title' => $movie->title,
                'posterUrl' => $movie->poster_path ? "https://image.tmdb.org/t/p/w500{$movie->poster_path}" : null,
                'year' => $movie->release_date ? substr($movie->release_date, 0, 4) : '',
                'rating' => $rating ? (int) round($rating->rating / 2) : 0,
                'watchedAt' => optional($movie->pivot)->added_at?->toISOString(),
            ];
        }) : collect([]);

        return response()->json([
            'userId' => $user->id,
            'watched' => $movies->values(),
        ]);
    }

    /**
     * Add movie to watched list
     * POST /api/watched
     * Body: { tmdb_id, title, rating, poster_path, release_date }
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'tmdb_id' => 'required|integer',
            'title' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'poster_path' => 'nullable|string',
            'release_date' => 'nullable|string',
        ]);

        // Find or create movie locally
        $movie = Movie::firstOrCreate(
            ['tmdb_id' => $data['tmdb_id']],
            [
                'title' => $data['title'],
                'poster_path' => $data['poster_path'] ?? null,
                'release_date' => $data['release_date'] ?? null,
            ]
        );

        // Add to "İzlediklerim" watchlist
        $watched = Watchlist::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'İzlediklerim'],
            ['description' => 'İzlediğim filmler', 'is_public' => false]
        );

        if (!$watched->movies()->where('movie_id', $movie->id)->exists()) {
            $watched->movies()->attach($movie->id, ['added_at' => now()]);
        }

        // Save rating if provided
        if (!empty($data['rating'])) {
            Rating::updateOrCreate(
                ['user_id' => $user->id, 'movie_id' => $movie->id],
                ['rating' => $data['rating'] * 2] // Convert 1-5 scale to 1-10
            );
        }

        return response()->json([
            'success' => true,
            'movie' => ['id' => $movie->id, 'title' => $movie->title],
        ], 201);
    }

    /**
     * Remove movie from watched list
     * DELETE /api/watched/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $user = request()->user();
        $movie = Movie::findOrFail($id);

        $watched = Watchlist::where('user_id', $user->id)
            ->where('name', 'İzlediklerim')
            ->first();

        if ($watched) {
            $watched->movies()->detach($movie->id);
        }

        return response()->json(['success' => true]);
    }
}
