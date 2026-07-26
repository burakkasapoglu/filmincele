<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\Rating;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RatingStars extends Component
{
    public int $tmdbId;
    public string $mediaType = 'movie';
    public ?int $userRating = null;
    public string $review = '';
    public bool $showReview = false;
    private ?int $localMovieId = null;

    public function mount(int $movieId): void
    {
        $this->tmdbId = $movieId;

        if (Auth::check()) {
            $this->ensureLocalMovie();
            if ($this->localMovieId) {
                $rating = Rating::where('user_id', Auth::id())
                    ->where('movie_id', $this->localMovieId)
                    ->first();
                if ($rating) {
                    $this->userRating = $rating->rating;
                    $this->review = $rating->review ?? '';
                }
            }
        }
    }

    public function setRating(int $rating): void
    {
        if (!Auth::check()) return;
        $this->ensureLocalMovie();
        if (!$this->localMovieId) return;

        Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'movie_id' => $this->localMovieId],
            ['rating' => $rating]
        );

        $this->userRating = $rating;
        $this->showReview = true;
    }

    public function saveReview(): void
    {
        if (!Auth::check()) return;
        if (!$this->localMovieId) return;

        Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'movie_id' => $this->localMovieId],
            ['review' => $this->review]
        );

        $this->showReview = false;
    }

    private function ensureLocalMovie(): void
    {
        $localMovie = Movie::where('tmdb_id', $this->tmdbId)->first();
        if ($localMovie) {
            $this->localMovieId = $localMovie->id;
            return;
        }

        $tmdb = app(TmdbService::class);

        if ($this->mediaType === 'tv') {
            $data = $tmdb->getTVDetails($this->tmdbId);
            if (!$data) return;
            $localMovie = Movie::firstOrCreate(
                ['tmdb_id' => $this->tmdbId],
                [
                    'title' => $data['name'] ?? '',
                    'title_original' => $data['original_name'] ?? '',
                    'overview' => $data['overview'] ?? '',
                    'poster_path' => $data['poster_path'] ?? '',
                    'backdrop_path' => $data['backdrop_path'] ?? '',
                    'release_date' => $data['first_air_date'] ?? null,
                    'vote_average' => $data['vote_average'] ?? 0,
                    'vote_count' => $data['vote_count'] ?? 0,
                    'popularity' => $data['popularity'] ?? 0,
                ]
            );
            $this->localMovieId = $localMovie->id;
            return;
        }

        $data = $tmdb->getMovieDetails($this->tmdbId);
        if (!$data) return;

        $localMovie = Movie::firstOrCreate(
            ['tmdb_id' => $this->tmdbId],
            [
                'title' => $data['title'] ?? '',
                'title_original' => $data['original_title'] ?? '',
                'overview' => $data['overview'] ?? '',
                'poster_path' => $data['poster_path'] ?? '',
                'backdrop_path' => $data['backdrop_path'] ?? '',
                'release_date' => $data['release_date'] ?? null,
                'runtime' => $data['runtime'] ?? null,
                'vote_average' => $data['vote_average'] ?? 0,
                'vote_count' => $data['vote_count'] ?? 0,
                'popularity' => $data['popularity'] ?? 0,
            ]
        );

        $this->localMovieId = $localMovie->id;
    }

    public function render()
    {
        return view('livewire.rating-stars');
    }
}
