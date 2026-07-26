<?php

namespace App\Services;

use App\Models\Post;
use App\Models\SocialShare;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    private string $fbPageId;
    private string $fbToken;
    private string $igAccountId;
    private string $baseFbUrl = 'https://graph.facebook.com/v19.0';

    public function __construct()
    {
        $this->fbPageId = config('social-media.facebook.page_id', '');
        $this->fbToken = config('social-media.facebook.access_token', '');
        $this->igAccountId = config('social-media.instagram.business_account_id', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->fbPageId) && !empty($this->fbToken);
    }

    public function publishPost(Post $post): array
    {
        $results = [];

        if ($this->isConfigured()) {
            $results['facebook'] = $this->postToFacebook($post);
        }

        if ($this->isConfigured() && !empty($this->igAccountId)) {
            $results['instagram'] = $this->postToInstagram($post);
        }

        return $results;
    }

    private function postToFacebook(Post $post): array
    {
        $url = route('blog.show', $post->slug);
        $message = "📝 Yeni Blog Yazısı\n\n{$post->title}\n\n{$post->excerpt}\n\nDevamını oku: {$url}";

        try {
            if ($post->image_url) {
                $response = Http::timeout(15)->post("{$this->baseFbUrl}/{$this->fbPageId}/photos", [
                    'access_token' => $this->fbToken,
                    'url' => $post->image_url,
                    'message' => $message,
                ]);
            } else {
                $response = Http::timeout(15)->post("{$this->baseFbUrl}/{$this->fbPageId}/feed", [
                    'access_token' => $this->fbToken,
                    'message' => $message,
                    'link' => $url,
                ]);
            }

            $data = $response->json();

            if ($response->successful() && isset($data['id'])) {
                SocialShare::create([
                    'post_id' => $post->id,
                    'platform' => 'facebook',
                    'share_url' => "https://facebook.com/{$data['id']}",
                    'share_id' => $data['id'],
                ]);

                Log::info("Facebook paylaşıldı: {$post->title}", ['id' => $data['id']]);
                return ['success' => true, 'id' => $data['id']];
            }

            $error = $data['error']['message'] ?? 'Bilinmeyen hata';
            Log::error("Facebook paylaşım hatası: {$error}");

            SocialShare::create([
                'post_id' => $post->id,
                'platform' => 'facebook',
                'error_message' => $error,
            ]);

            return ['success' => false, 'error' => $error];

        } catch (\Exception $e) {
            Log::error("Facebook paylaşım exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function postToInstagram(Post $post): array
    {
        $caption = "📝 {$post->title}\n\n{$post->excerpt}\n\nDevamını oku: " . route('blog.show', $post->slug) . "\n\n#film #sinema #filmincele";

        try {
            if (!$post->image_url) {
                return ['success' => false, 'error' => 'Instagram paylaşımı için görsel gerekli'];
            }

            $createResponse = Http::timeout(15)->post("{$this->baseFbUrl}/{$this->igAccountId}/media", [
                'access_token' => $this->fbToken,
                'image_url' => $post->image_url,
                'caption' => $caption,
            ]);

            $createData = $createResponse->json();

            if (!$createResponse->successful() || !isset($createData['id'])) {
                $error = $createData['error']['message'] ?? 'Container oluşturulamadı';
                Log::error("Instagram container hatası: {$error}");

                SocialShare::create([
                    'post_id' => $post->id,
                    'platform' => 'instagram',
                    'error_message' => $error,
                ]);

                return ['success' => false, 'error' => $error];
            }

            $containerId = $createData['id'];
            sleep(5);

            $publishResponse = Http::timeout(15)->post("{$this->baseFbUrl}/{$this->igAccountId}/media_publish", [
                'access_token' => $this->fbToken,
                'creation_id' => $containerId,
            ]);

            $publishData = $publishResponse->json();

            if ($publishResponse->successful() && isset($publishData['id'])) {
                SocialShare::create([
                    'post_id' => $post->id,
                    'platform' => 'instagram',
                    'share_url' => "https://instagram.com/p/{$publishData['id']}",
                    'share_id' => $publishData['id'],
                ]);

                Log::info("Instagram paylaşıldı: {$post->title}", ['id' => $publishData['id']]);
                return ['success' => true, 'id' => $publishData['id']];
            }

            $error = $publishData['error']['message'] ?? 'Yayınlama başarısız';
            Log::error("Instagram yayınlama hatası: {$error}");
            return ['success' => false, 'error' => $error];

        } catch (\Exception $e) {
            Log::error("Instagram paylaşım exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function shareLatestUnsharedPost(): ?array
    {
        $post = Post::where('is_published', true)
            ->whereDoesntHave('socialShares')
            ->latest('published_at')
            ->first();

        if (!$post) {
            Log::info('Paylaşılacak yeni blog yazısı yok.');
            return null;
        }

        Log::info("Otomatik paylaşım başlıyor: {$post->title}");
        return $this->publishPost($post);
    }
}
