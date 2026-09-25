<?php

return [
    'default' => 'bn',
    'fallback' => 'bn',
    'supported' => [
        'bn' => [
            'code' => 'bn',
            'name' => 'বাংলা',
            'english_name' => 'Bangla',
            'locale' => 'bn_BD',
            'direction' => 'ltr',
            'font_family_sans' => "'Noto Sans Bengali', system-ui, sans-serif",
            'font_family_serif' => "'Noto Serif Bengali', 'SolaimanLipi', Georgia, serif",
        ],
        'en' => [
            'code' => 'en',
            'name' => 'English',
            'english_name' => 'English',
            'locale' => 'en_US',
            'direction' => 'ltr',
            'font_family_sans' => "'Inter', system-ui, sans-serif",
            'font_family_serif' => "'Noto Serif', 'Source Serif Pro', Georgia, serif",
        ],
    ],
    'cookie_name' => 'sps_locale',
    'cookie_lifetime_days' => 365,
];
