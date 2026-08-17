<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DailyBlogScheduler
{
    public function runIfNeeded(): void
    {
        try {
            if (now()->format('H') < 7) {
                return;
            }

            if (Post::whereDate('published_at', now()->toDateString())->exists()) {
                return;
            }

            $attemptKey = 'daily-blog:attempted:' . now()->toDateString();
            if (Cache::get($attemptKey)) {
                return;
            }

            if (!Cache::add('daily-blog:running', true, 600)) {
                return;
            }

            Cache::put($attemptKey, true, now()->endOfDay());

            try {
                $post = app(BlogGeneratorService::class)->generate();

                if ($post) {
                    Log::info("Günlük yazı oluşturuldu: {$post->title}");
                    $this->refreshSitemap();
                } else {
                    Log::warning('Günlük yazı üretilemedi (konu verisi yok).');
                }
            } finally {
                Cache::forget('daily-blog:running');
            }
        } catch (\Throwable $e) {
            Log::warning('DailyBlogScheduler hatası: ' . $e->getMessage());
        }
    }

    private function refreshSitemap(): void
    {
        try {
            \Artisan::call('sitemap:generate');
        } catch (\Throwable $e) {
            Log::warning('Sitemap yenilenemedi: ' . $e->getMessage());
        }
    }
}
