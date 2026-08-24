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
    public bool $inCinemas = false;

    public function mount(int $tmdbId, TmdbService $tmdb): void
    {
        $this->tmdbId = $tmdbId;

        $data = $tmdb->getMovieDetails($tmdbId);

        if (!$data) {
            abort(404, 'Film bulunamadı');
        }

        $this->movie = $data;
        $this->credits = $data['credits'] ?? null;
        $this->videos = $data['videos']['results'] ?? [];
        $this->recommendations = $data['recommendations']['results'] ?? [];
        $this->trailerUrl = $tmdb->getTrailerUrl($this->videos);
        $this->watchProviders = $tmdb->getWatchProviders($tmdbId);

        $this->localMovie = Movie::where('tmdb_id', $tmdbId)
            ->with(['ratings' => fn ($q) => $q->with('user')->latest()])
            ->first();

        $this->inCinemas = $this->resolveInCinemas($tmdb, $tmdbId, $data);
    }

    private function resolveInCinemas(TmdbService $tmdb, int $tmdbId, array $data): bool
    {
        $inNowPlaying = cache()->remember('now-playing-ids:' . now()->toDateString(), 3600, function () use ($tmdb) {
            $ids = [];
            foreach ($tmdb->getNowPlaying() as $m) {
                $ids[] = (int) ($m['id'] ?? 0);
            }
            return array_flip(array_filter($ids));
        });

        if (isset($inNowPlaying[$tmdbId])) return true;

        // TR vizyon tarihi son 6 hafta icindeyse ve hala listede degilse emin olamayiz; sadece tarih kontrolu
        $trDate = $data['tr_release_date'] ?? $data['release_date'] ?? null;
        if ($trDate) {
            try {
                return \Carbon\Carbon::parse(substr($trDate, 0, 10))->between(now()->subWeeks(6), now());
            } catch (\Exception) {
                return false;
            }
        }

        return false;
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
            'inCinemas' => $this->inCinemas,
        ]);
    }
}
