<?php

namespace App\Livewire;

use Livewire\Component;

class MoodSelector extends Component
{
    public ?string $selectedMood = null;

    public function selectMood(string $mood): void
    {
        $this->selectedMood = $mood;
        $this->dispatch('mood-selected', mood: $mood);
    }

    public function render()
    {
        return view('livewire.mood-selector', [
            'moods' => config('moods'),
        ]);
    }
}
