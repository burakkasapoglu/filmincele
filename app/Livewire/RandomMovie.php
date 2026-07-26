<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Component;

class RandomMovie extends Component
{
    public bool $showPicker = false;

    public function togglePicker(): void
    {
        $this->showPicker = !$this->showPicker;
    }

    public function pickMovie(TmdbService $tmdb): void
    {
        $this->pick('movie', $tmdb);
    }

    public function pickTV(TmdbService $tmdb): void
    {
        $this->pick('tv', $tmdb);
    }

    private function pick(string $type, TmdbService $tmdb): void
    {
        $randomPage = rand(1, 20);
        $randomGenres = collect(config('moods'))->except(['+18', 'turk'])->random();

        $genreIds = $type === 'tv'
            ? ($randomGenres['tv_genres'] ?? $randomGenres['genres'])
            : $randomGenres['genres'];

        if ($type === 'tv') {
            $items = $tmdb->discoverTV([
                'with_genres' => implode(',', $genreIds),
                'sort_by' => 'popularity.desc',
                'page' => $randomPage,
            ]);
            if (empty($items)) {
                $items = $tmdb->getPopularTV($randomPage);
            }
        } else {
            $items = $tmdb->discoverMovies([
                'with_genres' => implode(',', $genreIds),
                'sort_by' => 'popularity.desc',
                'page' => $randomPage,
            ]);
            if (empty($items)) {
                $items = $tmdb->getPopularMovies($randomPage);
            }
        }

        if (!empty($items)) {
            $item = $items[array_rand($items)];
            $name = $item['title'] ?? $item['name'] ?? '';
            $url = $type === 'tv' ? dizi_url($item['id'], $name) : film_url($item['id'], $name);
            $this->redirect($url, navigate: false);
        }
    }

    public function render()
    {
        return view('livewire.random-movie');
    }
}
