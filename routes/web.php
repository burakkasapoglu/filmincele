<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/mod/{mood}', function (string $mood) {
    $moods = config('moods');
    if (!isset($moods[$mood])) {
        abort(404);
    }
    return view('mood', ['mood' => $mood]);
})->middleware('age-gate')->name('mood');

Route::get('/film/{idAndSlug}', function (string $idAndSlug) {
    $parts = explode('-', $idAndSlug);
    $tmdbId = (int) $parts[0];
    $slug = implode('-', array_slice($parts, 1));

    $data = app(\App\Services\TmdbService::class)->getMovieDetails($tmdbId);
    if (!$data) abort(404);

    $canonicalSlug = \Illuminate\Support\Str::slug($data['title'] ?? '');
    if ($slug !== $canonicalSlug) {
        return redirect()->to('/film/' . $tmdbId . ($canonicalSlug ? '-' . $canonicalSlug : ''), 301);
    }

    return view('movie-detail', ['tmdbId' => $tmdbId]);
})->name('movie.show');

Route::get('/dizi/{idAndSlug}', function (string $idAndSlug) {
    $parts = explode('-', $idAndSlug);
    $tmdbId = (int) $parts[0];
    $slug = implode('-', array_slice($parts, 1));

    $data = app(\App\Services\TmdbService::class)->getTVDetails($tmdbId);
    if (!$data) abort(404);

    $canonicalSlug = \Illuminate\Support\Str::slug($data['name'] ?? '');
    if ($slug !== $canonicalSlug) {
        return redirect()->to('/dizi/' . $tmdbId . ($canonicalSlug ? '-' . $canonicalSlug : ''), 301);
    }

    return view('tv-detail', ['tmdbId' => $tmdbId]);
})->name('tv.show');

Route::get('/kisi/{idAndSlug}', function (string $idAndSlug) {
    $parts = explode('-', $idAndSlug);
    $tmdbId = (int) $parts[0];
    $slug = implode('-', array_slice($parts, 1));

    $data = app(\App\Services\TmdbService::class)->getPersonDetails($tmdbId);
    if (!$data) abort(404);

    $canonicalSlug = \Illuminate\Support\Str::slug($data['name'] ?? '');
    if ($slug !== $canonicalSlug) {
        return redirect()->to('/kisi/' . $tmdbId . ($canonicalSlug ? '-' . $canonicalSlug : ''), 301);
    }

    return view('person-detail', ['tmdbId' => $tmdbId]);
})->name('person.show');

Route::get('/kesfet', function () {
    return view('discover');
})->name('discover');

Route::get('/vizyonda', function () {
    return view('now-playing');
})->name('now-playing');

Route::get('/yakinda', function () {
    return view('upcoming');
})->name('upcoming');

Route::get('/platform/{providerId}/{name?}', function (int $providerId, ?string $name = null) {
    return view('platform', ['providerId' => $providerId, 'name' => $name]);
})->name('platform');

Route::get('/sirket/{companyId}/{name?}', function (int $companyId, ?string $name = null) {
    return view('company', ['companyId' => $companyId, 'name' => $name]);
})->name('company');

Route::get('/ulke/{countryCode}/{name?}', function (string $countryCode, ?string $name = null) {
    return view('country', ['countryCode' => $countryCode, 'name' => $name]);
})->name('country');

Route::get('/blog', function () {
    return view('blog');
})->name('blog');

Route::get('/blog/{slug}', function (string $slug) {
    $post = \App\Models\Post::where('slug', $slug)->firstOrFail();
    return view('blog-detail', ['post' => $post]);
})->name('blog.show');

Route::get('/giris', function () {
    return view('auth.login');
});

Route::get('/kayit', function () {
    return view('auth.register');
});

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /profil\nDisallow: /giris\nDisallow: /kayit\n\nSitemap: " . url('/sitemap_index.xml'), 200)->header('Content-Type', 'text/plain');
});

Route::get('/cron/gunluk-yazi/{token}', function (string $token) {
    if (!config('services.daily_blog.token') || !hash_equals(config('services.daily_blog.token'), $token)) {
        abort(404);
    }
    app(\App\Services\DailyBlogScheduler::class)->runIfNeeded();
    return response('OK');
});

Route::get('/kvkk', function () {
    return view('kvkk');
})->name('kvkk');

Route::get('/liste/{token}', function (string $token) {
    $list = \App\Models\Watchlist::where('share_token', $token)->with(['user', 'movies'])->firstOrFail();
    return view('shared-list', ['list' => $list]);
})->name('shared-list');

Route::get('/google{code}.html', function (string $code) {
    $verification = env('GOOGLE_SEARCH_CONSOLE', '');
    if ($code === $verification || $code === '/' . $verification) {
        return response("google-site-verification: {$verification}.html", 200)->header('Content-Type', 'text/html');
    }
    abort(404);
})->where('code', '.*');

Route::post('/deploy/{token}', function (string $token) {
    if ($token !== env('DEPLOY_TOKEN', '')) abort(403);
    $output = shell_exec('cd ' . base_path() . ' && git pull origin main 2>&1 && composer install --no-dev --optimize-autoloader 2>&1 && php artisan migrate --force 2>&1 && php artisan optimize 2>&1 && php artisan sitemap:generate 2>&1');
    return response('<pre>' . $output . '</pre>');
});

Route::get('/storage/{path}', function (string $path) {
    $file = storage_path('app/public/' . $path);
    if (!file_exists($file)) abort(404);
    return response()->file($file);
})->where('path', '.*');

Route::get('/koleksiyon/{slug}', function (string $slug) {
    $collections = config('collections');
    if (!isset($collections[$slug])) abort(404);
    return view('collection', ['collection' => $collections[$slug], 'slug' => $slug]);
})->name('collection');

Route::get('/karsilastir/{id1?}/{id2?}', function (?int $id1 = null, ?int $id2 = null) {
    return view('compare', ['id1' => $id1, 'id2' => $id2]);
})->name('compare');

Route::get('/istatistikler', function () {
    return view('stats');
})->name('stats');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profil', function () {
        return view('profile');
    })->name('profile');

    Route::get('/profil/duzenle', function () {
        return view('edit-profile');
    })->name('profile.edit');

    Route::get('/dashboard', function () {
        return redirect('/profil');
    });
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/uyeler', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
    Route::get('/icerik-fikirleri', [\App\Http\Controllers\Admin\AdminController::class, 'contentIdeas'])->name('ideas');
    Route::post('/icerik-fikirleri/durum', [\App\Http\Controllers\Admin\AdminController::class, 'updateContentIdeaStatus'])->name('ideas.status');
    Route::post('/icerik-fikirleri/uret', [\App\Http\Controllers\Admin\AdminController::class, 'generateFromIdea'])->name('ideas.generate');

    Route::get('/uyeler/{user}', [\App\Http\Controllers\Admin\AdminController::class, 'userDetail'])->name('users.show');
    Route::post('/uyeler/{user}/toggle-admin', [\App\Http\Controllers\Admin\AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::get('/blog', [\App\Http\Controllers\Admin\AdminController::class, 'posts'])->name('posts');
    Route::get('/blog/yeni', [\App\Http\Controllers\Admin\AdminController::class, 'createPost'])->name('posts.create');
    Route::post('/blog/yeni', [\App\Http\Controllers\Admin\AdminController::class, 'storePost'])->name('posts.store');
    Route::get('/blog/{post}/duzenle', [\App\Http\Controllers\Admin\AdminController::class, 'editPost'])->name('posts.edit');
    Route::put('/blog/{post}/duzenle', [\App\Http\Controllers\Admin\AdminController::class, 'updatePost'])->name('posts.update');
    Route::delete('/blog/{post}', [\App\Http\Controllers\Admin\AdminController::class, 'deletePost'])->name('posts.delete');
    Route::post('/blog/{post}/share', [\App\Http\Controllers\Admin\AdminController::class, 'sharePost'])->name('posts.share');
    Route::post('/blog/image-upload', [\App\Http\Controllers\Admin\AdminController::class, 'uploadImage'])->name('posts.image-upload');
});
