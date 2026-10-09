<?php

return [
    'name' => 'SANATAN PHILOSOPHY AND SCRIPTURE',
    'short_name' => 'SPS',
    'motto' => 'Steadfast in Sanatan Unity, Propagation & Welfare',
    'motto_bn' => 'সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল।',
    'env' => 'development',
    'debug' => true,
    'base_url' => '',
    'timezone' => 'Asia/Dhaka',
    'version' => '1.0.0-phase1',

    // Official SPS Social Media & Community Channels
    'social' => [
        'facebook' => 'https://www.facebook.com/bewithsps',
        'youtube' => 'https://www.youtube.com/@spsofficial1529',
        'blog' => 'https://sanatanphilosophyandscripture.blogspot.com',
        'instagram' => 'https://www.instagram.com/bewithsps/',
    ],

    // Institutional Helplines & Contacts
    'contacts' => [
        'primary' => '+8801736360041',
        'secondary' => '+880 1782-009415',
        'tertiary' => '+880 1736-360041',
        'email' => 'contact@sps-platform.org',
    ],

    // Google Identity Services (GIS) / OAuth 2.0 Security Proof Gateway
    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID') ?: '',
        'allowed_admin_emails' => [
            'badhanamitroy571@gmail.com',
            'anik@sps.org',
            'president@sps.org',
            'general.secretary@sps.org',
        ],
    ],
];

