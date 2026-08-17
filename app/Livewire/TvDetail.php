<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Component;

class TvDetail extends Component
{
    public int $tmdbId;
    public ?array $series = null;
    public ?array $credits = null;
    public ?array $videos = null;
    public ?array $recommendations = null;
    public ?string $trailerUrl = null;
    public ?array $watchProviders = null;
    public ?array $seasons = [];

    public function mount(int $tmdbId, TmdbService $tmdb): void
    {
        $this->tmdbId = $tmdbId;
        $data = $tmdb->getTVDetails($tmdbId);

        if (!$data) {
            abort(404, 'Dizi bulunamadı');
        }

        $this->series = $data;
        $this->credits = $data['credits'] ?? null;
        $this->videos = $data['videos']['results'] ?? [];
        $this->recommendations = $data['recommendations']['results'] ?? [];
        $this->trailerUrl = $tmdb->getTrailerUrl($this->videos);
        $this->seasons = $data['seasons'] ?? [];
        $this->watchProviders = $data['watch/providers'] ?? null;
    }

    public function render()
    {
        return view('livewire.tv-detail', [
            'series' => $this->series,
            'credits' => $this->credits,
            'videos' => $this->videos,
            'recommendations' => $this->recommendations,
            'trailerUrl' => $this->trailerUrl,
            'seasons' => $this->seasons,
            'watchProviders' => $this->watchProviders,
        ]);
    }
}
