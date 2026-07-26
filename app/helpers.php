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
