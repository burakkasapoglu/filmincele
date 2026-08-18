<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentIdea extends Model
{
    protected $fillable = [
        'title', 'suggestion', 'type', 'event_date', 'tmdb_ref',
        'status', 'notes', 'post_id', 'script',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
