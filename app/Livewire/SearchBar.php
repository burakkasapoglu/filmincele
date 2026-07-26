<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Component;

class SearchBar extends Component
{
    public string $query = '';
    public array $suggestions = [];
    public bool $showDropdown = false;

    public function updatedQuery(string $value): void
    {
        if (strlen(trim($value)) < 2) {
            $this->suggestions = [];
            $this->showDropdown = false;
            return;
        }

        $tmdb = app(TmdbService::class);
        $results = $tmdb->searchMulti(trim($value));

        $this->suggestions = array_slice(
            array_filter($results, fn($r) => in_array($r['media_type'] ?? '', ['movie', 'person'])),
            0,
            8
        );

        $this->showDropdown = !empty($this->suggestions);
    }

    public function search(): void
    {
        if (empty(trim($this->query))) return;
        $this->showDropdown = false;
        $this->redirect(url('/kesfet?q=' . urlencode($this->query)), navigate: false);
    }

    public function selectSuggestion(int $id, string $mediaType): void
    {
        $this->showDropdown = false;
        $this->query = '';

        if ($mediaType === 'movie') {
            $this->redirect(url('/film/' . $id), navigate: false);
        } elseif ($mediaType === 'tv') {
            $this->redirect(url('/dizi/' . $id), navigate: false);
        } elseif ($mediaType === 'person') {
            $this->redirect(url('/kisi/' . $id), navigate: false);
        }
    }

    public function render()
    {
        return view('livewire.search-bar', [
            'suggestions' => $this->suggestions,
        ]);
    }
}
