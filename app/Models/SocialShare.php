<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialShare extends Model
{
    protected $fillable = ['post_id', 'platform', 'share_url', 'share_id', 'error_message'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
