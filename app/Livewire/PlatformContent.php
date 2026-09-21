<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Attributes\Url;
use Livewire\Component;

class PlatformContent extends Component
{
    public int $providerId;
    public string $providerName = '';

    #[Url]
    public string $tab = 'movie';

    #[Url]
    public int $page = 1;

    public array $items = [];
    public int $totalPages = 1;

    public function mount(TmdbService $tmdb, int $providerId, ?string $name = null): void
    {
        $this->providerId = $providerId;
        $this->providerName = $this->resolveName($name) ?? $tmdb->getProviderName($providerId) ?? 'Platform';
        $this->loadPage($tmdb);
    }

    public function switchTab(string $tab): void
    {
        $this->tab = $tab === 'tv' ? 'tv' : 'movie';
        $this->page = 1;
        $this->loadPage();
    }

    public function goToPage(int $p): void
    {
        $this->page = max(1, $p);
        $this->loadPage();
    }

    private function resolveName(?string $name): ?string
    {
        if (!$name) return null;
        foreach (config('platforms') as $p) {
            if ($p['id'] === $this->providerId) return $p['name'];
        }
        return ucfirst(str_replace('-', ' ', $name));
    }

    private function loadPage(?TmdbService $tmdb = null): void
    {
        $tmdb = $tmdb ?? app(TmdbService::class);
        $page = $this->page;

        if ($this->tab === 'tv') {
            $data = $tmdb->discoverTV([
                'with_watch_providers' => $this->providerId,
                'watch_region' => 'TR',
                'sort_by' => 'popularity.desc',
                'page' => $page,
            ]);
            $this->items = $data['results'] ?? [];
            $this->totalPages = $data['total_pages'] ?? 1;
        } else {
            $data = $tmdb->fetchDiscoverMovie($page, $this->providerId);
            $this->items = $data['results'] ?? [];
            $this->totalPages = $data['total_pages'] ?? 1;
        }

        $this->totalPages = min(max((int) $this->totalPages, 1), 500);
    }

    public function render()
    {
        return view('livewire.platform-content', [
            'provider' => collect(config('platforms'))->firstWhere('id', $this->providerId),
        ]);
    }
}