<?php

namespace App\Console\Commands;

use App\Services\SocialMediaService;
use Illuminate\Console\Command;

class SocialAutoPublish extends Command
{
    protected $signature = 'social:auto-publish';
    protected $description = 'En son yayınlanmamış blog yazısını sosyal medyada paylaş';

    public function handle(SocialMediaService $social): int
    {
        if (!$social->isConfigured()) {
            $this->info('Sosyal medya hesapları yapılandırılmamış. .env dosyasına FACEBOOK_ bilgilerini ekleyin.');
            return self::SUCCESS;
        }

        $this->info('Sosyal medya otomatik paylaşım başlıyor...');

        $results = $social->shareLatestUnsharedPost();

        if ($results === null) {
            $this->info('Paylaşılacak yeni yazı bulunamadı.');
            return self::SUCCESS;
        }

        foreach ($results as $platform => $result) {
            if ($result['success'] ?? false) {
                $this->info("✓ {$platform}: Paylaşıldı (ID: {$result['id']})");
            } else {
                $this->error("✗ {$platform}: {$result['error']}");
            }
        }

        return self::SUCCESS;
    }
}
