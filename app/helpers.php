<?php

if (!function_exists('film_url')) {
    function film_url(int $id, ?string $title = null): string
    {
        $slug = \Illuminate\Support\Str::slug($title ?? '');
        return url('/film/' . $id . ($slug ? '-' . $slug : ''));
    }
}

if (!function_exists('dizi_url')) {
    function dizi_url(int $id, ?string $name = null): string
    {
        $slug = \Illuminate\Support\Str::slug($name ?? '');
        return url('/dizi/' . $id . ($slug ? '-' . $slug : ''));
    }
}

if (!function_exists('kisi_url')) {
    function kisi_url(int $id, ?string $name = null): string
    {
        $slug = \Illuminate\Support\Str::slug($name ?? '');
        return url('/kisi/' . $id . ($slug ? '-' . $slug : ''));
    }
}

if (!function_exists('cult_poster')) {
    /**
     * Poster yoksa deterministik rastgele kult film afisi URL'i dondur.
     */
    function cult_poster(?string $seed = null, string $size = 'w342'): string
    {
        $cult = [
            '7T2SDS5efuJiK45oyKoEzf9RKjw', // Esaretin Bedeli
            'vseIVRdN4xasYwStQIi6SI7DcEu', // Baba
            '6ZNnKbdDRQm0ftkq3OKiDrwZkIN', // Yesil Yol
            'Cw4hIUIAmSYfK9QfaUW5igp9La', // Forrest Gump
            'dXNAPwY7VrqMAo51EKhhCJfaGb5', // Matrix
            'yjMuqAyJUoQZGWsZ0vZuYj5inAR', // Dogus Kulubu
            'AgY33Wtg4737MhYopJSFyKWhKsO', // Ucuz Roman
            '7IPCEr7ifdH5CtU97QG7XgAAtOp', // Kara Sovalye
            'xn0Kcg4e6p0mLxVS3nAWhNmW2Ni', // Baslangic
            'wiSuje8hdVuwM0pvhtSFirCHmJF', // Prestij
            'oMPDt1rNLYEVRpigNLaiTnibVn8', // Siki Dostlar
            'xvOEOMCzfV8qXkd1n1btZ8q4Psd', // Ruhlarin Kacisi
            '9F7xtVA6cKGdjkVu2s33C09oZAP', // Yarali Yuz
            '8rUEO5BjmWEQcGUm5SmO134NBC3', // Yuruyen Sato
            '2mOH8EqumIaepdP94e0cy4Xnyg7', // Schindler'in Listesi
        ];

        $key = $seed ? crc32($seed) : random_int(0, count($cult) - 1);
        $path = $cult[abs($key) % count($cult)];

        return 'https://image.tmdb.org/t/p/' . $size . '/' . $path . '.jpg';
    }
}
