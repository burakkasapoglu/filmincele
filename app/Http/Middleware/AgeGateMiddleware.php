<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgeGateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $mood = $request->route('mood');
        $moods = config('moods');

        if (!isset($moods[$mood]) || empty($moods[$mood]['adult'])) {
            return $next($request);
        }

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', '+18 içerikleri görüntülemek için giriş yapmalısınız.');
        }

        if (!Auth::user()->isAdult()) {
            return redirect('/')->with('error', '+18 içerikleri görüntülemek için 18 yaşından büyük olmalısınız. Doğum tarihinizi profilinizden güncelleyebilirsiniz.');
        }

        return $next($request);
    }
}
