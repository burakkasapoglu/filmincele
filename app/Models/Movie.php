<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id', 'title', 'title_original', 'overview',
        'poster_path', 'backdrop_path', 'release_date', 'runtime',
        'vote_average', 'vote_count', 'popularity', 'slug',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($movie) {
            if (empty($movie->slug) && $movie->title) {
                $movie->slug = Str::slug($movie->title);
            }
        });
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'movie_person')
            ->withPivot(['role_type', 'job', 'character', 'order_no'])
            ->orderBy('order_no');
    }

    public function cast(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'movie_person')
            ->withPivot(['character', 'order_no'])
            ->wherePivot('role_type', 'cast')
            ->orderBy('order_no');
    }

    public function crew(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'movie_person')
            ->withPivot(['job', 'order_no'])
            ->wherePivot('role_type', 'crew')
            ->orderBy('order_no');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function avgRating(): float
    {
        return round($this->ratings()->avg('rating') ?? 0, 1);
    }

    public function posterUrl(string $size = 'w500'): ?string
    {
        if (!$this->poster_path) return null;
        return 'https://image.tmdb.org/t/p/' . $size . $this->poster_path;
    }

    public function backdropUrl(string $size = 'w1280'): ?string
    {
        if (!$this->backdrop_path) return null;
        return 'https://image.tmdb.org/t/p/' . $size . $this->backdrop_path;
    }
}
