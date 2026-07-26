<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MovieGrid extends Component
{
    use WithPagination;

    public string $mood = 'aksiyon';
    public string $sortBy = 'popularity.desc';
    public string $mediaType = 'movie';
    public ?int $yearFrom = null;
    public ?int $yearTo = null;
    public ?int $ratingMin = null;
    public ?string $region = null;
    public ?string $watchProvider = null;
    public int $perPage = 20;

    protected $queryString = ['mood', 'sortBy', 'mediaType', 'yearFrom', 'yearTo', 'ratingMin', 'region', 'watchProvider'];

    public function mount(string $mood = 'aksiyon'): void
    {
        $this->mood = $mood;
        $moodsConfig = config('moods');
        $moodData = $moodsConfig[$this->mood] ?? [];

        if (!empty($moodData['adult'])) {
            if (!Auth::check() || !Auth::user()->isAdult()) {
                abort(403);
            }
        }
    }

    public function updated($property): void
    {
        if (in_array($property, ['mood', 'mediaType', 'sortBy', 'yearFrom', 'yearTo', 'ratingMin', 'region', 'watchProvider'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->sortBy = 'popularity.desc';
        $this->yearFrom = null;
        $this->yearTo = null;
        $this->ratingMin = null;
        $this->region = null;
        $this->watchProvider = null;
        $this->resetPage();
    }

    public function render(TmdbService $tmdb)
    {
        $moodsConfig = config('moods');
        $moodData = $moodsConfig[$this->mood] ?? $moodsConfig['aksiyon'];

        $genreIds = $this->mediaType === 'tv'
            ? ($moodData['tv_genres'] ?? $moodData['genres'])
            : $moodData['genres'];

        $filters = [
            'sort_by' => $this->sortBy,
            'page' => $this->getPage(),
            'vote_count.gte' => $this->mediaType === 'tv' ? 10 : 30,
        ];

        if (!empty($genreIds)) {
            $filters['with_genres'] = implode(',', $genreIds);
        }

        if (!empty($moodData['origin_country'])) {
            $filters['with_origin_country'] = $moodData['origin_country'];
        }

        if ($this->region && $this->region !== 'all') {
            if ($this->region === 'western') {
                $filters['without_genres'] = '16';
                $filters['vote_count.gte'] = 50;
            } else {
                $filters['with_origin_country'] = $this->region;
            }
        }

        if ($this->watchProvider && $this->watchProvider !== 'all') {
            $filters['with_watch_providers'] = $this->watchProvider;
            $filters['watch_region'] = 'TR';
        }

        if (!empty($moodData['adult'])) {
            $filters['include_adult'] = 'true';
            $filters['vote_count.gte'] = $this->mediaType === 'tv' ? 5 : 10;
        }

        if ($this->ratingMin) {
            $filters['vote_average.gte'] = $this->ratingMin;
        }

        if ($this->yearFrom) {
            $filters[$this->mediaType === 'tv' ? 'first_air_date.gte' : 'primary_release_date.gte'] = $this->yearFrom . '-01-01';
        }
        if ($this->yearTo) {
            $filters[$this->mediaType === 'tv' ? 'first_air_date.lte' : 'primary_release_date.lte'] = $this->yearTo . '-12-31';
        }

        $movies = $this->mediaType === 'tv'
            ? $tmdb->discoverTV($filters)
            : $tmdb->discoverMovies($filters);

        $currentYear = (int) now()->format('Y');

        return view('livewire.movie-grid', [
            'movies' => $movies,
            'moodData' => $moodData,
            'currentYear' => $currentYear,
        ]);
    }

    protected function getPage(): int
    {
        return (int) request()->get('page', 1);
    }
}
