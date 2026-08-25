<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Attributes\Url;
use Livewire\Component;

class PosterGallery extends Component
{
    #[Url]
    public int $page = 1;

    public array $items = [];
    public int $totalPages = 1;

    public function mount(TmdbService $tmdb): void
    {
        $this->page = max(1, min($this->page, 500));
        $this->loadPage($this->page);
    }

    public function goToPage(int $p): void
    {
        $this->page = max(1, min($p, 500));
        $this->loadPage($this->page);
    }

    private function loadPage(int $page): void
    {
        $data = app(TmdbService::class)->getPostersPage($page);
        $this->items = $data['items'];
        $this->totalPages = $data['totalPages'];
    }

    public function render()
    {
        return view('livewire.poster-gallery');
    }
}
