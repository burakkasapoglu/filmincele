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
            $this->weeklyPrune();

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

    /**
     * Haftalik bir kere: dosya cache birikimini buda (inode limiti korumasi).
     * 14 gunden eski cache dosyalarini ve oturumlari siler; taze cache durur.
     */
    private function weeklyPrune(): void
    {
        $weekKey = 'storage-pruned:' . now()->format('o-W');
        if (Cache::get($weekKey)) {
            return;
        }

        if (!Cache::add('storage-pruning', true, 600)) {
            return;
        }

        Cache::put($weekKey, true, now()->addDays(8));

        try {
            $cutoff = now()->subDays(14)->getTimestamp();
            $deleted = 0;

            $cacheDir = storage_path('framework/cache/data');
            if (is_dir($cacheDir)) {
                $it = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($cacheDir, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($it as $file) {
                    if ($file->isFile() && $file->getMTime() < $cutoff) {
                        @unlink($file->getPathname());
                        $deleted++;
                    }
                }
            }

            $sessionDir = storage_path('framework/sessions');
            if (is_dir($sessionDir)) {
                foreach (new \FilesystemIterator($sessionDir, \FilesystemIterator::SKIP_DOTS) as $file) {
                    if ($file->isFile() && $file->getMTime() < $cutoff) {
                        @unlink($file->getPathname());
                    }
                }
            }

            Log::info("Haftalik cache budama: {$deleted} eski dosya silindi.");
        } catch (\Throwable $e) {
            Log::warning('Cache budama hatası: ' . $e->getMessage());
        } finally {
            Cache::forget('storage-pruning');
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
