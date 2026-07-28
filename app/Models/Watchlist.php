<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Watchlist extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'is_public', 'sort_order', 'share_token'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($list) {
            if (empty($list->share_token)) {
                $list->share_token = \Illuminate\Support\Str::random(16);
            }
            if ($list->is_public && empty($list->share_token)) {
                $list->share_token = \Illuminate\Support\Str::random(16);
            }
        });

        static::updating(function ($list) {
            if ($list->is_public && empty($list->share_token)) {
                $list->share_token = \Illuminate\Support\Str::random(16);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'watchlist_movie')
            ->withPivot(['notes', 'added_at']);
    }
}
