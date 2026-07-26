<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'birth_date',
        'bio',
        'location',
        'website',
        'profile_photo_path',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            if (str_starts_with($this->profile_photo_path, 'http')) {
                return $this->profile_photo_path;
            }
            return asset('storage/' . $this->profile_photo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=fff&background=f43f5e&size=128';
    }

    public function age(): ?int
    {
        if (!$this->birth_date) return null;
        return \Carbon\Carbon::parse($this->birth_date)->age;
    }

    public function isAdult(): bool
    {
        return ($this->age() ?? 0) >= 18;
    }

    public function ratings()
    {
        return $this->hasMany(\App\Models\Rating::class);
    }

    public function watchlists()
    {
        return $this->hasMany(\App\Models\Watchlist::class);
    }

    public function favoritePeople()
    {
        return $this->belongsToMany(\App\Models\Person::class, 'user_favorite_people');
    }

    public function favoriteGenres()
    {
        return $this->belongsToMany(\App\Models\Genre::class, 'user_favorite_genres');
    }

    public function followers()
    {
        return $this->hasMany(\App\Models\Follow::class, 'following_id');
    }

    public function following()
    {
        return $this->hasMany(\App\Models\Follow::class, 'follower_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected static function booted(): void
    {
        static::created(function ($user) {
            $user->watchlists()->createMany([
                ['name' => 'İzlediklerim', 'description' => 'İzlediğim filmler', 'is_public' => false],
                ['name' => 'İzleyeceklerim', 'description' => 'İzlemek istediğim filmler', 'is_public' => false],
            ]);
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }
}
