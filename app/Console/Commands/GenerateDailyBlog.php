<?php

namespace App\Console\Commands;

use App\Services\BlogGeneratorService;
use App\Services\SocialMediaService;
use Illuminate\Console\Command;

class GenerateDailyBlog extends Command
{
    protected $signature = 'blog:generate-daily';
    protected $description = 'Otomatik günlük blog yazısı oluştur ve sosyal medyada paylaş';

    public function handle(BlogGeneratorService $generator, SocialMediaService $social): int
    {
        $this->info('Günlük blog yazısı oluşturuluyor...');

        $post = $generator->generate();

        if (!$post) {
            $this->warn('Bugün için içerik üretilemedi (yeterli veri yok).');
            return self::SUCCESS;
        }

        $this->info("✓ Yazı oluşturuldu: {$post->title}");

        if ($social->isConfigured()) {
            $this->info('Sosyal medya paylaşımı yapılıyor...');
            $results = $social->publishPost($post);

            foreach ($results as $platform => $result) {
                if ($result['success'] ?? false) {
                    $this->info("  ✓ {$platform}: Paylaşıldı");
                } else {
                    $this->warn("  ✗ {$platform}: {$result['error']}");
                }
            }
        } else {
            $this->info('Sosyal medya yapılandırılmadı, paylaşım atlandı.');
        }

        return self::SUCCESS;
    }
}
