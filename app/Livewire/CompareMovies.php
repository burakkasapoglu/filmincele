<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Component;

class CompareMovies extends Component
{
    public string $search1 = '';
    public string $search2 = '';
    public array $suggestions1 = [];
    public array $suggestions2 = [];
    public ?array $movie1 = null;
    public ?array $movie2 = null;
    public ?int $id1 = null;
    public ?int $id2 = null;

    public function mount(?int $id1 = null, ?int $id2 = null): void
    {
        $tmdb = app(TmdbService::class);
        if ($id1) { $this->movie1 = $tmdb->getMovieDetails($id1); $this->id1 = $id1; }
        if ($id2) { $this->movie2 = $tmdb->getMovieDetails($id2); $this->id2 = $id2; }
    }

    public function updatedSearch1(): void
    {
        if (strlen($this->search1) < 2) { $this->suggestions1 = []; return; }
        $tmdb = app(TmdbService::class);
        $this->suggestions1 = array_slice(
            array_filter($tmdb->searchMovies($this->search1), fn($r) => ($r['vote_count'] ?? 0) > 10),
            0, 6
        );
    }

    public function updatedSearch2(): void
    {
        if (strlen($this->search2) < 2) { $this->suggestions2 = []; return; }
        $tmdb = app(TmdbService::class);
        $this->suggestions2 = array_slice(
            array_filter($tmdb->searchMovies($this->search2), fn($r) => ($r['vote_count'] ?? 0) > 10),
            0, 6
        );
    }

    public function select1(int $id): void
    {
        $tmdb = app(TmdbService::class);
        $this->movie1 = $tmdb->getMovieDetails($id);
        $this->id1 = $id;
        $this->search1 = '';
        $this->suggestions1 = [];
    }

    public function select2(int $id): void
    {
        $tmdb = app(TmdbService::class);
        $this->movie2 = $tmdb->getMovieDetails($id);
        $this->id2 = $id;
        $this->search2 = '';
        $this->suggestions2 = [];
    }

    public function clear1(): void { $this->movie1 = null; $this->id1 = null; }
    public function clear2(): void { $this->movie2 = null; $this->id2 = null; }

    public function render()
    {
        return view('livewire.compare-movies');
    }
}
