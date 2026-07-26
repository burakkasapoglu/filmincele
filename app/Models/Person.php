<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Person extends Model
{
    protected $fillable = [
        'tmdb_id', 'name', 'photo_path', 'biography',
        'birth_date', 'known_for_department', 'popularity',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_person')
            ->withPivot(['role_type', 'job', 'character', 'order_no']);
    }

    public function photoUrl(string $size = 'w342'): ?string
    {
        if (!$this->photo_path) return null;
        return 'https://image.tmdb.org/t/p/' . $size . $this->photo_path;
    }
}
