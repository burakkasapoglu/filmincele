<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class EditProfile extends Component
{
    public string $name = '';
    public string $email = '';
    public ?string $birthDate = null;
    public string $bio = '';
    public string $location = '';
    public string $website = '';
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';
    public bool $showPasswordForm = false;
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->birthDate = $user->birth_date
            ? (is_string($user->birth_date) ? $user->birth_date : $user->birth_date->format('Y-m-d'))
            : null;
        $this->bio = $user->bio ?? '';
        $this->location = $user->location ?? '';
        $this->website = $user->website ?? '';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'birthDate' => ['nullable', 'date', 'before_or_equal:' . now()->subYears(13)->format('Y-m-d')],
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:200',
        ], ['birthDate.before_or_equal' => 'En az 13 yaşında olmalısınız.']);

        Auth::user()->update([
            'name' => $this->name,
            'birth_date' => $this->birthDate ?: null,
            'bio' => $this->bio ?: null,
            'location' => $this->location ?: null,
            'website' => $this->website ?: null,
        ]);

        $this->successMessage = 'Profil güncellendi.';
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($this->currentPassword, Auth::user()->password)) {
            $this->errorMessage = 'Mevcut şifre yanlış.';
            return;
        }

        Auth::user()->update(['password' => Hash::make($this->newPassword)]);
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        $this->showPasswordForm = false;
        $this->successMessage = 'Şifre değiştirildi.';
    }

    public function render()
    {
        return view('livewire.edit-profile');
    }
}
