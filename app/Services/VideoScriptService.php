<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VideoScriptService
{
    private ?string $apiKey = null;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent';

    public ?string $lastError = null;
    public array $lastFails = [];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?: null;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Sosyal medya videosu için konuşma metni + çekim planı üret.
     *
     * $verifiedItems: TMDB'den dogrulanmis icerik listesi.
     * Gemini YALNIZCA bu listedeki film/dizilerden bahsedebilir;
     * çikti tarandıkca list disi/unverified ad kullanilmissa uretim reddedilir ve
     * kısıtli prompt ile yeniden denenir. Hâlâ basarisizsa null döner.
     *
     * @param array $verifiedItems [['id'=>int,'title'=>string,'year'=>string,'overview'=>string,'type'=>'movie'|'tv']]
     */
    public function generateScript(string $topic, array $verifiedItems = []): ?array
    {
        $this->lastError = null;

        if (!$this->isConfigured()) {
            $this->lastError = 'Gemini API anahtarı tanımlı değil.';
            return null;
        }

        if (empty($verifiedItems)) {
            $verifiedItems = $this->fallbackVerifiedItems($topic);
        }

        if (empty($verifiedItems)) {
            $this->lastError = 'Konu için TMDB üzerinden doğrulanmış içerik bulunamadı. Fikir tekrar üretilmeli.';
            return null;
        }

        $attempts = 0;
        while ($attempts < 3) {
            $attempts++;

            $prompt = $this->buildPrompt($topic, $verifiedItems, $attempts > 1 ? $this->lastFails : []);
            $result = $this->callGemini($prompt);

            if (!$result) {
                return null;
            }

            $unknown = $this->validateReferences($result, $verifiedItems);

            // Uydurma ad yoksa da: hicbir icerik adi isaretlenmemis ise (kose parantez
            // yoksa) senaryo saf yaraticiliga kaymis olabilir — reddet ve yeniden iste.
            if (empty($unknown)) {
                $refCount = $this->countBracketedReferences($result);
                $expected = min(count($verifiedItems), 3);
                if ($refCount === 0 && count($verifiedItems) > 0) {
                    $unknown = ['(hicbir dogrulanmis icerik adi kullanilmadi — en az 1 tanesini [Ad] formunda kullan)'];
                }
            }

            if (empty($unknown)) {
                return $result;
            }

            $this->lastFails = $unknown;
            Log::warning('VideoScript uydurma icerik tespit edildi, tekrar deneniyor: ' . implode(', ', $unknown));
        }

        $this->lastError = 'Yapay zeka gerçekte olmayan içerik adları üretti (' . implode(', ', $this->lastFails ?? []) . '). Otomatik yeniden denemeler başarısız oldu, fikri güncelleyip tekrar deneyin.';
        return null;
    }

    private function buildPrompt(string $topic, array $items, array $previousFails): string
    {
        $list = '';
        foreach ($items as $i => $item) {
            $overview = \Illuminate\Support\Str::limit($item['overview'] ?? '', 200);
            $list .= ($i + 1) . ". {$item['title']}" . (!empty($item['year']) ? " ({$item['year']})" : '')
                . " [" . ($item['type'] ?? 'movie') . "]\n"
                . "    Ozgecmis: " . ($overview ?: 'bulunamadi') . "\n";
        }

        $failNote = '';
        if (!empty($previousFails)) {
            $failNote = "\n\nUYARI: Onceki denemenizde ASAGIDAKI adlar dogrulanamadi ve gercekte yok: "
                . implode(', ', $previousFails)
                . ". Bu adlari KESINLIKLE bir daha kullanma. Sadece verilen listedekileri kullan.";
        }

        return "Sen filmincele.com'un sosyal medya video içerik üreticisisin. Kanal sahibi kamera önünde konuşan bir sunucu; videolar Instagram Reels / TikTok / YouTube Shorts formatında.

KONU: {$topic}

DOGRULANMIS IÇERIK LISTESI (yalnizca bu listeyi kullan):
{$list}

GÖREV: Konu için 60-90 saniyelik bir video metni hazırla. Blog yazisi DEGIL, birebir kamerada okunacak konuşma metni olmalı.

DOGRULUK KURALLARI (EN ÖNEMLISI):
- Senaryoda gecen HER film/dizi adi, yukaridaki DOGRULANMIS LISTEDEN olmalidir. Liste disinda hicbir film/dizi adi kullanma.
- Gercekte var olmayan, listede olmayan film/dizi/fragman UYDURMA. Emin olmadigin bilgileri genel ifadelerle anlat (ornek: 'bu listedeki bir gerilim filmi' yerine listekinden ad ver).
- Film adlarini tum metaciklarda (video_title, script, hook, visual_notes, sm_caption) KOSE PARANTEZ ICINDE ver: [Film Adi]. Ornek: Sizi sasirtacak bir film: [Resident Evil].
- Yil, yonetmen, puan gibi somut bilgi verirken sadece listedeki iceriklerin bilgilerini kullan; bilmiyorsan yil degeriyle sinirla kendini.
- Uydurma icerik tespit edilirse metin REDDEDILIR. Dogruluk > yaraticilik.
{$failNote}

DIGER KURALLAR:
- Ilk 3 saniyede izleyiciyi yakalayan bir 'hook' cumlesi ile basla (soru, iddia veya sok edici bilgi)
- Konusma dili kullan: kisa cumleler, samimi anlatim
- Sunucu birinci agizdan konussun ('Bugun size gosterecegim...' gibi)
- Sonunda izleyiciye cagri yap: sence hangisi gibi dogal bir soru
- Film adi gecerken cekim notunda hangi sahnenin gosterilecegini belirt
- Video basligi kisa ve meraklandirici olsun (max 60 karakter)
- Sosyal medya aciklamasi 1-2 cumle + 8-12 hashtag (Turkce agirlikli)

SADECE JSON dondur: {\"video_title\":\"...\",\"hook\":\"ilk 3 saniye cumlesi\",\"script\":\"konusma metni (paragraflar halinde)\",\"visual_notes\":[\"sahne 1: ...\",\"sahne 2: ...\"],\"sm_caption\":\"sosyal medya aciklamasi\",\"hashtags\":[\"#...\",\"#...\"]}";
    }

    private function callGemini(string $prompt): ?array
    {
        try {
            $response = Http::timeout(60)->post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 4000],
            ]);

            if (!$response->successful()) {
                Log::warning('VideoScript Gemini HTTP failed', ['status' => $response->status()]);
                $this->lastError = 'Gemini isteği başarısız (HTTP ' . $response->status() . ').';
                return null;
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!$text) {
                $this->lastError = 'Gemini boş yanıt döndü.';
                return null;
            }

            $text = trim(str_replace(['```json', '```'], '', $text));
            $result = json_decode($text, true);

            if (!$result || !isset($result['video_title'], $result['script'])) {
                $this->lastError = 'Gemini geçerli JSON döndürmedi, tekrar deneyin.';
                return null;
            }

            return $result;
        } catch (\Exception $e) {
            Log::warning('VideoScript exception: ' . $e->getMessage());
            $this->lastError = 'Sunucu hatası: ' . $e->getMessage();
            return null;
        }
    }

    /**
     * Metindeki [Ad] referanslarini dogrulanmis listeyle esleştirir.
     * Liste disi kde var → uydurma olarak dondurur.
     */
    private function validateReferences(array $result, array $verifiedItems): array
    {
        $validTitles = [];
        foreach ($verifiedItems as $item) {
            $validTitles[] = mb_strtolower(trim($item['title']));
        }

        $fields = array_filter([$result['video_title'] ?? '', $result['hook'] ?? '', $result['script'] ?? '', $result['sm_caption'] ?? '']);
        $fields = array_merge($fields, $result['visual_notes'] ?? []);

        $text = implode("\n", $fields);

        // [Ad] formunda gecen tum referanslar
        preg_match_all('/\[([^\]]{2,80})\]/u', $text, $m);
        $unknown = [];
        foreach ($m[1] as $ref) {
            $refClean = mb_strtolower(trim($ref));
            // yil/parantez temizlik
            $refBase = trim(preg_replace('/\s*\(\d{4}\)\s*$/u', '', $refClean));
            $refBase = trim(preg_replace('/\s*(\-|\|)\s*(film|dizi|dizisi)$/u', '', $refBase));

            $matched = false;
            foreach ($validTitles as $vt) {
                $vtBase = trim(preg_replace('/\s*\(\d{4}\)\s*$/u', '', $vt));
                if ($refBase === $vt || $refBase === $vtBase || str_contains($vt, $refBase) || str_contains($refBase, $vt)) {
                    // Kucuk eslesmelerde asiri esnek olma: kisa ve genel kelimeler eslesmesin
                    if (mb_strlen($refBase) < 5 && $refBase !== $vtBase) {
                        continue;
                    }
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $unknown[] = $ref;
            }
        }

        return array_values(array_unique($unknown));
    }

    private function countBracketedReferences(array $result): int
    {
        $fields = array_filter([$result['hook'] ?? '', $result['script'] ?? '']);
        $text = implode("\n", $fields);
        preg_match_all('/\[([^\]]{2,80})\]/u', $text, $m);
        return count($m[1]);
    }

    /**
     * Topic metnini TMDB'de arayip dogrulanmis cikti listesi kurar.
     */
    private function fallbackVerifiedItems(string $topic): array
    {
        $tmdb = app(TmdbService::class);
        $items = [];

        foreach (array_merge($tmdb->searchMulti($topic), $tmdb->getTrending('week')) as $res) {
            if (count($items) >= 8) break;
            $type = $res['media_type'] ?? 'movie';
            if (!in_array($type, ['movie', 'tv'])) continue;
            $title = $res['title'] ?? $res['name'] ?? null;
            if (!$title) continue;
            $items[] = [
                'id' => $res['id'],
                'title' => $title,
                'year' => substr($res['release_date'] ?? $res['first_air_date'] ?? '', 0, 4),
                'overview' => $res['overview'] ?? '',
                'type' => $type,
            ];
        }

        return $items;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}