<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate sitemap.xml and sitemap_index.xml';

    public function handle(): int
    {
        $this->info('Sitemap oluşturuluyor...');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        $pages = [
            ['url' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['url' => url('/kesfet'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['url' => url('/blog'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['url' => url('/vizyonda'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['url' => url('/yakinda'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['url' => url('/karsilastir'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['url' => url('/istatistikler'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['url' => url('/kvkk'), 'changefreq' => 'monthly', 'priority' => '0.3'],
        ];

        foreach (config('moods') as $slug => $mood) {
            $pages[] = ['url' => url("/mod/{$slug}"), 'changefreq' => 'daily', 'priority' => '0.7'];
        }

        foreach (config('collections') as $slug => $coll) {
            $pages[] = ['url' => url("/koleksiyon/{$slug}"), 'changefreq' => 'weekly', 'priority' => '0.7'];
        }

        foreach ($pages as $page) {
            $xml .= "  <url>" . PHP_EOL;
            $xml .= "    <loc>{$page['url']}</loc>" . PHP_EOL;
            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>" . PHP_EOL;
            $xml .= "    <priority>{$page['priority']}</priority>" . PHP_EOL;
            $xml .= "  </url>" . PHP_EOL;
        }

        foreach (Post::where('is_published', true)->get() as $post) {
            $xml .= "  <url>" . PHP_EOL;
            $xml .= '    <loc>' . url("/blog/{$post->slug}") . '</loc>' . PHP_EOL;
            $xml .= "    <lastmod>{$post->updated_at->toAtomString()}</lastmod>" . PHP_EOL;
            $xml .= "    <changefreq>monthly</changefreq>" . PHP_EOL;
            $xml .= "    <priority>0.6</priority>" . PHP_EOL;
            $xml .= "  </url>" . PHP_EOL;
        }

        $xml .= '</urlset>';
        file_put_contents(public_path('sitemap.xml'), $xml);
        $this->info('✓ sitemap.xml oluşturuldu');

        $indexXml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $indexXml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $indexXml .= '  <sitemap>' . PHP_EOL;
        $indexXml .= '    <loc>' . url('/sitemap.xml') . '</loc>' . PHP_EOL;
        $indexXml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
        $indexXml .= '  </sitemap>' . PHP_EOL;
        $indexXml .= '</sitemapindex>';
        file_put_contents(public_path('sitemap_index.xml'), $indexXml);
        $this->info('✓ sitemap_index.xml oluşturuldu');

        return self::SUCCESS;
    }
}
