<?php

/*
|--------------------------------------------------------------------------
| Türkiye Platformlari
|--------------------------------------------------------------------------
|
| TMDB watch provider ID'leri ve platform meta bilgileri.
| Turkiye'de film/dizi izlenebilen ana platformlar.
|
*/

return [
    // Ana abonelik platformlari
    ['id' => 8,    'name' => 'Netflix',            'type' => 'both',  'color' => '#E50914', 'emoji' => '🎬'],
    ['id' => 119,  'name' => 'Amazon Prime Video', 'type' => 'both',  'color' => '#00A8E1', 'emoji' => '📺'],
    ['id' => 337,  'name' => 'Disney Plus',        'type' => 'both',  'color' => '#113CCF', 'emoji' => '🏰'],
    ['id' => 1899, 'name' => 'HBO Max',            'type' => 'both',  'color' => '#8B5CF6', 'emoji' => '🎭'],
    ['id' => 1826, 'name' => 'TOD TV',             'type' => 'both',  'color' => '#F59E0B', 'emoji' => '⚽'],
    ['id' => 1904, 'name' => 'TV+',               'type' => 'both',  'color' => '#DC2626', 'emoji' => '📡'],

    // Yerli platformlar
    ['id' => 342,  'name' => 'puhutv',             'type' => 'both',  'color' => '#FF6600', 'emoji' => '🇹🇷'],
    ['id' => 1791, 'name' => 'Exxen',              'type' => 'both',  'color' => '#F59E0B', 'emoji' => '⚽'],
    ['id' => 1833, 'name' => 'Tivibu',             'type' => 'both',  'color' => '#0EA5E9', 'emoji' => '📺'],

    // Nis/zaman zaman abonelik
    ['id' => 11,   'name' => 'MUBI',               'type' => 'both',  'color' => '#000000', 'emoji' => '🎞️'],
    ['id' => 283,  'name' => 'Crunchyroll',        'type' => 'both',  'color' => '#F47521', 'emoji' => '🍙'],
    ['id' => 188,  'name' => 'YouTube Premium',    'type' => 'both',  'color' => '#FF0000', 'emoji' => '▶️'],

    // Kiralama / satin alma
    ['id' => 2,    'name' => 'Apple TV Store',     'type' => 'rent',  'color' => '#A2AAAD', 'emoji' => '🍎'],
    ['id' => 3,    'name' => 'Google Play Movies', 'type' => 'rent',  'color' => '#34A853', 'emoji' => '▶️'],
];
