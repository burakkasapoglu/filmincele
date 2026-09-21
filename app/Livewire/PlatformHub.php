<?php

namespace App\Livewire;

use Livewire\Component;

class PlatformHub extends Component
{
    public array $platforms = [];
    public array $counts = [];

    public function mount(): void
    {
        $this->platforms = config('platforms');
        $this->counts = \Illuminate\Support\Facades\Cache::remember('platform-hub-counts', 21600, function () {
            $tmdb = app(\App\Services\TmdbService::class);
            $out = [];
            foreach ($this->platforms as $p) {
                $movies = [];
                if (in_array($p['type'], ['both', 'rent'])) {
                    $movies = $tmdb->getProviderMovies($p['id']);
                }
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
