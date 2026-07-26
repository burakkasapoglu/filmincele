<?php

namespace App\Livewire;

use App\Services\TmdbService;
use Livewire\Component;

class TodayInCinema extends Component
{
    public array $birthdayGroups = [];
    public array $anniversaryMovies = [];
    public string $todayDate = '';

    public function mount(TmdbService $tmdb): void
    {
        $today = now();
        $this->todayDate = $today->format('d.m.Y');

        $this->loadBirthdays($tmdb, $today);
        $this->loadAnniversaryMovies($tmdb, $today);
    }

    private function loadBirthdays(TmdbService $tmdb, $today): void
    {
        $allBirthdays = collect(config('cinema-calendar'));

        $dates = [
            'Dün' => $today->copy()->subDay(),
            'Bugün' => $today->copy(),
            'Yarın' => $today->copy()->addDay(),
        ];

        $this->birthdayGroups = [];

        foreach ($dates as $label => $date) {
            $monthDay = $date->format('m-d');
            $matches = $allBirthdays
                ->filter(fn($p) => substr($p['birthday'], 5) === $monthDay)
                ->values();

            if ($matches->isEmpty()) continue;

            $enriched = $matches->map(function ($entry) use ($tmdb) {
                $details = $tmdb->getPersonDetails($entry['tmdb_id']);
                $age = null;
                if ($entry['birthday'] && !str_contains($entry['birthday'], '0000')) {
                    $age = \Carbon\Carbon::parse($entry['birthday'])->age;
                }
                return array_merge($entry, [
                    'profile_path' => $details['profile_path'] ?? null,
                    'known_for_department' => $details['known_for_department'] ?? '',
                    'age' => $age,
                ]);
            })->values()->toArray();

            $this->birthdayGroups[] = [
                'label' => $label,
                'date' => $date->format('d.m.Y'),
                'people' => $enriched,
            ];
        }
    }

    private function loadAnniversaryMovies(TmdbService $tmdb, $today): void
    {
        $currentMonth = $today->format('m');
        $currentDay = $today->format('d');

        $allMovies = [];
        for ($year = 1985; $year <= 2025; $year++) {
            $results = $tmdb->discoverMovies([
                'primary_release_date.gte' => "{$year}-{$currentMonth}-{$currentDay}",
                'primary_release_date.lte' => "{$year}-{$currentMonth}-{$currentDay}",
                'sort_by' => 'vote_count.desc',
            ]);
            $allMovies = array_merge($allMovies, $results);
            if (count($allMovies) >= 12) break;
        }

        usort($allMovies, function ($a, $b) {
            return ($b['vote_count'] ?? 0) <=> ($a['vote_count'] ?? 0);
        });

        $this->anniversaryMovies = array_slice($allMovies, 0, 12);
    }

    public function render()
    {
        return view('livewire.today-in-cinema', [
            'birthdayGroups' => $this->birthdayGroups,
            'anniversaryMovies' => $this->anniversaryMovies,
            'todayDate' => $this->todayDate,
        ]);
    }
}
