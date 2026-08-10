<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TrackPageViews
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $url = $request->path();
            if (!str_starts_with($url, 'livewire') && !str_starts_with($url, 'api')) {
                $movieId = null;
                if (preg_match('#^/(?:film|dizi)/(\d+)#', '/' . $url, $m)) {
                    $movieId = (int) $m[1];
                }

                PageView::create([
                    'url' => '/' . $url,
                    'route_name' => $request->route()?->getName(),
                    'ip_address' => $request->ip(),
                    'user_agent' => Str::limit($request->userAgent(), 500),
                    'user_id' => Auth::id(),
                    'session_id' => $request->session()->getId(),
                    'movie_id' => $movieId,
                ]);
            }
        } catch (\Exception $e) {
            // DB limit dolu veya yetki yok — sessizce devam et
        }

        return $response;
    }
}
