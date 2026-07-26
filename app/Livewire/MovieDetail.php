<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Services\TmdbService;
use Livewire\Component;

class MovieDetail extends Component
{
    public int $tmdbId;
    public ?array $movie = null;
    public ?array $credits = null;
    public ?array $videos = null;
    public ?array $recommendations = null;
    public ?string $trailerUrl = null;
    public ?array $watchProviders = null;
    public ?Movie $localMovie = null;

    public function mount(int $tmdbId, TmdbService $tmdb): void
    {
        $this->tmdbId = $tmdbId;

        $data = $tmdb->getMovieDetails($tmdbId);

        if ($data) {
            $this->movie = $data;
            $this->credits = $data['credits'] ?? null;
            $this->videos = $data['videos']['results'] ?? [];
            $this->recommendations = $data['recommendations']['results'] ?? [];
            $this->trailerUrl = $tmdb->getTrailerUrl($this->videos);
            $this->watchProviders = $tmdb->getWatchProviders($tmdbId);

            $this->localMovie = Movie::where('tmdb_id', $tmdbId)->first();
        }
    }

    public function render()
    {
        return view('livewire.movie-detail', [
            'movie' => $this->movie,
            'credits' => $this->credits,
            'videos' => $this->videos,
            'recommendations' => $this->recommendations,
            'trailerUrl' => $this->trailerUrl,
            'watchProviders' => $this->watchProviders,
            'localMovie' => $this->localMovie,
        ]);
    }
}
