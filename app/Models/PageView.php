<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = ['url', 'route_name', 'ip_address', 'user_agent', 'user_id', 'session_id', 'movie_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
