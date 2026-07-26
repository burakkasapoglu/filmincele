<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FollowButton extends Component
{
    public User $user;
    public bool $isFollowing = false;

    public function mount(User $user): void
    {
        $this->user = $user;
        if (Auth::check()) {
            $this->isFollowing = Auth::user()->isFollowing($user);
        }
    }

    public function toggle(): void
    {
        if (!Auth::check() || Auth::id() === $this->user->id) return;

        if ($this->isFollowing) {
            Auth::user()->following()->where('following_id', $this->user->id)->delete();
            $this->isFollowing = false;
        } else {
            Auth::user()->following()->create(['following_id' => $this->user->id]);
            $this->isFollowing = true;
        }
    }

    public function render()
    {
        return view('livewire.follow-button');
    }
}
