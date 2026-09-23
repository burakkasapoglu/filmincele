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
        $provider = collect(config('platforms'))->firstWhere('id', $this->providerId);

        if ($this->tab === 'tv') {
            $data = $tmdb->fetchDiscoverTVPage($page, $this->providerId);
            $items = $data['results'] ?? [];
            $totalPages = $data['total_pages'] ?? 1;

            // Yerli platform: strict veri azsa Turk menseyli katalog ile birles ve doldur
            if (!empty($provider['local'])) {
                $merged = $this->mergeLocalContent($tmdb, $items, $data['total_results'] ?? count($items), 'tv', $page);
                $items = $merged['items'];
                $totalPages = $merged['totalPages'];
            }

            $this->items = $items;
            $this->totalPages = $totalPages;
        } else {
            $data = $tmdb->fetchDiscoverMovie($page, $this->providerId);
            $items = $data['results'] ?? [];
            $totalPages = $data['total_pages'] ?? 1;

            if (!empty($provider['local'])) {
                $merged = $this->mergeLocalContent($tmdb, $items, $data['total_results'] ?? count($items), 'movie', $page);
                $items = $merged['items'];
                $totalPages = $merged['totalPages'];
            }

            $this->items = $items;
            $this->totalPages = $totalPages;
        }

        $this->totalPages = min(max((int) $this->totalPages, 1), 500);
    }

    /**
     * Yerli platformlar icin strict sonuca ek olarak Turkiye menseine ait
     * genis katalogu birleştir — TMDB'nin yerli platform lisans eslemesi
     * zayif oldugundan Exxen/puhutv gibi platformlar bos kalmasin.
     */
    private function mergeLocalContent(TmdbService $tmdb, array $items, int $strictTotal, string $mediaType, int $page): array
    {
        $seen = [];
        foreach ($items as $it) {
            $seen[$it['id']] = true;
        }
        $merged = $items;

        $need = 20;
        if (count($merged) < $need) {
            $supplement = $tmdb->fetchDiscoverByOriginTR($page, $mediaType, $this->providerId);
            foreach ($supplement as $it) {
                if (count($merged) >= $need) break;
                if (isset($seen[$it['id']])) continue;
                $seen[$it['id']] = true;
                $merged[] = $it;
            }
        }

        return [
            'items' => $merged,
            'totalPages' => max((int) ceil((max($strictTotal, count($merged)) + 20) / 20), 1),
        ];
    }

    public function render()
    {
        return view('livewire.platform-content', [
            'provider' => collect(config('platforms'))->firstWhere('id', $this->providerId),
        ]);
    }
}