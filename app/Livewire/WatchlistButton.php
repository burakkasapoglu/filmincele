<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\Watchlist;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WatchlistButton extends Component
{
    public int $tmdbId;
    public string $mediaType = 'movie';
    public bool $isInWatchlist = false;
    public array $userWatchlists = [];
    public bool $showDropdown = false;
    public string $newListName = '';
    public bool $showNewListInput = false;
    public ?string $feedback = null;

    public function mount(int $tmdbId): void
    {
        $this->tmdbId = $tmdbId;
        $this->refreshState();
    }

    public function refreshState(): void
    {
        if (!Auth::check()) return;

        $userId = Auth::id();

        if (Watchlist::where('user_id', $userId)->count() === 0) {
            Watchlist::insert([
                ['user_id' => $userId, 'name' => 'İzlediklerim', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $userId, 'name' => 'İzleyeceklerim', 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->userWatchlists = Watchlist::where('user_id', $userId)
            ->withCount('movies')
            ->get()
            ->toArray();

        $localMovie = Movie::where('tmdb_id', $this->tmdbId)->first();
        if ($localMovie) {
            $this->isInWatchlist = Watchlist::where('user_id', $userId)
                ->whereHas('movies', fn($q) => $q->where('movie_id', $localMovie->id))
                ->exists();
        }
    }

    public function toggleDropdown(): void
    {
        if (!Auth::check()) return;
        $this->showDropdown = !$this->showDropdown;
        if ($this->showDropdown) {
            $this->refreshState();
        }
    }

    public function showNewList(): void
    {
        $this->showNewListInput = true;
        $this->showDropdown = true;
    }

    public function createAndAdd(): void
    {
        if (!Auth::check() || empty(trim($this->newListName))) return;

        $watchlist = Watchlist::create([
            'user_id' => Auth::id(),
            'name' => trim($this->newListName),
            'is_public' => true,
        ]);

        $this->addMovieToList($watchlist->id);
        $this->newListName = '';
        $this->showNewListInput = false;
        $this->showDropdown = false;
        $this->feedback = '"' . $watchlist->name . '" listesine eklendi.';
        $this->refreshState();
    }

    public function toggleList(int $watchlistId): void
    {
        if (!Auth::check()) return;

        $watchlist = Watchlist::find($watchlistId);
        if (!$watchlist || $watchlist->user_id !== Auth::id()) return;

        $this->addMovieToList($watchlistId);
        $this->showDropdown = false;
        $this->refreshState();

        if ($this->isInWatchlist) {
            $this->feedback = '"' . $watchlist->name . '" listesine eklendi.';
        } else {
            $this->feedback = '"' . $watchlist->name . '" listesinden çıkarıldı.';
        }
    }

    private function addMovieToList(int $watchlistId): void
    {
        $localMovie = $this->ensureLocalMovie();
        if (!$localMovie) return;

        $watchlist = Watchlist::find($watchlistId);
        if (!$watchlist || $watchlist->user_id !== Auth::id()) return;

        if ($watchlist->movies()->where('movie_id', $localMovie->id)->exists()) {
            $watchlist->movies()->detach($localMovie->id);
        } else {
            $watchlist->movies()->attach($localMovie->id, ['added_at' => now()]);
        }
    }

    private function ensureLocalMovie(): ?Movie
    {
        $localMovie = Movie::where('tmdb_id', $this->tmdbId)->first();
        if ($localMovie) return $localMovie;

        $tmdb = app(TmdbService::class);
        
        if ($this->mediaType === 'tv') {
            $data = $tmdb->getTVDetails($this->tmdbId);
            if (!$data) return null;
            return Movie::firstOrCreate(
                ['tmdb_id' => $this->tmdbId],
                [
                    'type' => 'tv',
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
        }

        $data = $tmdb->getMovieDetails($this->tmdbId);
        if (!$data) return null;

        return Movie::firstOrCreate(
            ['tmdb_id' => $this->tmdbId],
            [
                'type' => 'movie',
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
    }

    public function render()
    {
        return view('livewire.watchlist-button', [
            'isInWatchlist' => $this->isInWatchlist,
        ]);
    }
}
