<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class PlatformHub extends Component
{
    public array $platforms = [];
    public array $counts = [];
    public array $logos = [];

    public function mount(): void
    {
        $this->platforms = config('platforms');

        $tmdb = app(TmdbService::class);

        $this->logos = Cache::remember('platform-hub-logos', 86400, function () use ($tmdb) {
            $out = [];
            foreach ($this->platforms as $p) {
                $logo = $tmdb->getProviderLogo($p['id']);
                $out[$p['id']] = $logo;
            }
            return $out;
        });

        $this->counts = Cache::remember('platform-hub-counts', 21600, function () use ($tmdb) {
            $out = [];
            foreach ($this->platforms as $p) {
                $movies = in_array($p['type'], ['both', 'rent']) ? $tmdb->getProviderMovies($p['id']) : [];
                $tv = in_array($p['type'], ['both']) ? $tmdb->getProviderTVContent($p['id']) : [];
                $out[$p['id']] = min(count($movies), 40) + min(count($tv), 40);
            }
            return $out;
        });
    }

    public function render()
    {
        return view('livewire.platform-hub');
    }
}