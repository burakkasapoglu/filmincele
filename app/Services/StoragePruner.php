<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StoragePruner
{
    private const CACHE_MAX_AGE_DAYS = 2;
    private const SESSION_MAX_AGE_DAYS = 2;
    private const LOG_MAX_AGE_DAYS = 3;
    private const MAX_CACHE_FILES = 50000;

    public function runIfNeeded(): void
    {
        $dayKey = 'storage-pruned:' . now()->format('Y-m-d');
        if (Cache::get($dayKey)) {
            return;
        }

        if (!Cache::add('storage-pruning', true, 600)) {
            return;
        }

        try {
            $stats = $this->prune();
            Cache::put($dayKey, true, now()->endOfDay()->addDay());

            Log::info(sprintf(
                'Gunluk depolama budama: cache %d, kota %d, oturum %d, log %d dosya silindi (~%d MB).',
                $stats['cache'],
                $stats['cache_cap'],
                $stats['sessions'],
                $stats['logs'],
                (int) round($stats['bytes'] / 1048576)
            ));
        } catch (\Throwable $e) {
            Log::warning('Depolama budama hatasi: ' . $e->getMessage());
        } finally {
            Cache::forget('storage-pruning');
        }
    }

    public function prune(int $maxCacheFiles = self::MAX_CACHE_FILES): array
    {
        $stats = [
            'cache' => 0,
            'cache_cap' => 0,
            'sessions' => 0,
            'logs' => 0,
            'bytes' => 0,
        ];

        $this->pruneCacheData($stats, max(1, $maxCacheFiles));
        $this->pruneSessions($stats);
        $this->pruneLogs($stats);

        return $stats;
    }

    private function pruneCacheData(array &$stats, int $maxCacheFiles): void
    {
        $dir = storage_path('framework/cache/data');
        if (!is_dir($dir)) {
            return;
        }

        $cutoff = now()->subDays(self::CACHE_MAX_AGE_DAYS)->getTimestamp();
        $heap = new \SplMinHeap();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || str_starts_with($file->getFilename(), '.')) {
                continue;
            }

            $path = $file->getPathname();
            $mtime = $file->getMTime();

            if ($mtime < $cutoff) {
                $size = (int) $file->getSize();
                if (@unlink($path)) {
                    $stats['cache']++;
                    $stats['bytes'] += $size;
                }
                continue;
            }

            if ($heap->count() < $maxCacheFiles) {
                $heap->insert([$mtime, $path]);
                continue;
            }

            if ($mtime > $heap->top()[0]) {
                $evicted = $heap->extract();
                $size = (int) @filesize($evicted[1]);
                if (@unlink($evicted[1])) {
                    $stats['cache_cap']++;
                    $stats['bytes'] += $size;
                }
                $heap->insert([$mtime, $path]);
            } else {
                $size = (int) $file->getSize();
                if (@unlink($path)) {
                    $stats['cache_cap']++;
                    $stats['bytes'] += $size;
                }
            }
        }
    }

    private function pruneSessions(array &$stats): void
    {
        $dir = storage_path('framework/sessions');
        if (!is_dir($dir)) {
            return;
        }

        $cutoff = now()->subDays(self::SESSION_MAX_AGE_DAYS)->getTimestamp();

        foreach (new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS) as $file) {
            if (!$file->isFile() || str_starts_with($file->getFilename(), '.')) {
                continue;
            }

            if ($file->getMTime() < $cutoff) {
                $size = (int) $file->getSize();
                if (@unlink($file->getPathname())) {
                    $stats['sessions']++;
                    $stats['bytes'] += $size;
                }
            }
        }
    }

    private function pruneLogs(array &$stats): void
    {
        $dir = storage_path('logs');
        if (!is_dir($dir)) {
            return;
        }

        $cutoff = now()->subDays(self::LOG_MAX_AGE_DAYS)->getTimestamp();

        foreach (new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS) as $file) {
            if (!$file->isFile() || !preg_match('/^laravel-\d{4}-\d{2}-\d{2}\.log$/', $file->getFilename())) {
                continue;
            }

            if ($file->getMTime() < $cutoff) {
                $size = (int) $file->getSize();
                if (@unlink($file->getPathname())) {
                    $stats['logs']++;
                    $stats['bytes'] += $size;
                }
            }
        }
    }
}
