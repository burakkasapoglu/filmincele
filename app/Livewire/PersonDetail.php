<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Component;

class PersonDetail extends Component
{
    public int $tmdbId;
    public ?array $person = null;
    public ?string $imdbUrl = null;
    public ?string $instagramUrl = null;
    public ?string $twitterUrl = null;
    public ?int $yearsActive = null;
    public array $alsoKnownAs = [];
    public array $allCredits = [];
    public int $totalCount = 0;

    public function mount(int $tmdbId, TmdbService $tmdb): void
    {
        $this->tmdbId = $tmdbId;
        $data = $tmdb->getPersonDetails($tmdbId);

        if ($data) {
            $this->person = [
                'name' => $data['name'] ?? '',
                'biography' => $data['biography'] ?? '',
                'birthday' => $data['birthday'] ?? null,
                'deathday' => $data['deathday'] ?? null,
                'place_of_birth' => $data['place_of_birth'] ?? '',
                'known_for_department' => $data['known_for_department'] ?? '',
                'popularity' => $data['popularity'] ?? 0,
                'profile_path' => $data['profile_path'] ?? null,
            ];

            $extIds = $data['external_ids'] ?? null;
            if ($extIds) {
                if ($extIds['imdb_id'] ?? null) $this->imdbUrl = 'https://www.imdb.com/name/' . $extIds['imdb_id'];
                if ($extIds['instagram_id'] ?? null) $this->instagramUrl = 'https://www.instagram.com/' . $extIds['instagram_id'];
                if ($extIds['twitter_id'] ?? null) $this->twitterUrl = 'https://x.com/' . $extIds['twitter_id'];
            }

            $this->alsoKnownAs = array_values(array_filter($data['also_known_as'] ?? [], function ($name) {
                return preg_match('/^[a-zA-ZığüşöçİĞÜŞÖÇ .\-\']+$/u', $name) && strlen($name) > 1;
            }));

            $allItems = [];
            foreach (['movie_credits', 'tv_credits'] as $type) {
                foreach (['cast', 'crew'] as $role) {
                    foreach ($data[$type][$role] ?? [] as $item) {
                        $item['media_type'] = $type === 'tv_credits' ? 'tv' : 'movie';
                        $item['sort_date'] = $item['release_date'] ?? $item['first_air_date'] ?? '0000';
                        $allItems[] = $item;
                    }
                }
            }

            $seen = [];
            foreach ($allItems as $item) {
                $key = $item['media_type'] . '-' . $item['id'];
                if (!in_array($key, $seen)) {
                    $seen[] = $key;
                    $this->allCredits[] = $item;
                }
            }

            usort($this->allCredits, fn($a, $b) => strcmp($b['sort_date'], $a['sort_date']));
            $this->totalCount = count($this->allCredits);
            $this->allCredits = array_slice($this->allCredits, 0, 60);

            if (!empty($this->allCredits)) {
                $years = array_filter(array_map(fn($m) => $m['sort_date'] > '0000' ? (int) substr($m['sort_date'], 0, 4) : null, $this->allCredits));
                if (!empty($years)) $this->yearsActive = max($years) - min($years);
            }
        }
    }

    public function age(): ?int
    {
        if (!$this->person || empty($this->person['birthday'])) return null;
        return \Carbon\Carbon::parse($this->person['birthday'])->age;
    }

    public function render()
    {
        return view('livewire.person-detail', [
            'person' => $this->person,
            'allCredits' => $this->allCredits,
            'totalCount' => $this->totalCount,
            'alsoKnownAs' => $this->alsoKnownAs,
            'imdbUrl' => $this->imdbUrl,
            'instagramUrl' => $this->instagramUrl,
            'twitterUrl' => $this->twitterUrl,
            'age' => $this->age(),
            'yearsActive' => $this->yearsActive,
        ]);
    }
}
