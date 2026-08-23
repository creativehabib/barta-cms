<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Multilingual configuration
    |--------------------------------------------------------------------------
    | Barta stores translatable fields (post title/body, category name…) as
    | JSON keyed by locale using spatie/laravel-translatable.
    */
    'locales' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('BARTA_LOCALES', 'bn,en'))
    ))),

    'locale_names' => [
        'bn' => 'বাংলা',
        'en' => 'English',
    ],

    'default_locale' => env('APP_LOCALE', 'bn'),

    /*
    |--------------------------------------------------------------------------
    | Permalink structures
    |--------------------------------------------------------------------------
    | Controls how a single post URL is built. The active structure is stored
    | in settings (key: permalink_structure) and falls back to this env value.
    |   default   => /news/{slug}
    |   date      => /2026/08/{slug}
    |   month     => /2026/08/{slug}   (alias of date)
    |   day       => /2026/08/23/{slug}
    |   postname  => /{slug}
    |   category  => /{category}/{slug}
    */
    'permalink' => env('BARTA_PERMALINK', 'date'),

    'permalinks' => [
        'default'  => 'news/{slug}',
        'date'     => '{year}/{month}/{slug}',
        'day'      => '{year}/{month}/{day}/{slug}',
        'postname' => '{slug}',
        'category' => '{category}/{slug}',
        'custom'   => '{category}/{slug}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme & plugin engine
    |--------------------------------------------------------------------------
    */
    'themes_path'  => base_path('themes'),
    'plugins_path' => base_path('plugins'),
    'active_theme' => env('BARTA_ACTIVE_THEME', 'barta'),

    /*
    |--------------------------------------------------------------------------
    | Roles shipped by the installer
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'super-admin' => 'Super Admin',
        'admin'       => 'Admin',
        'editor'      => 'Editor',
        'author'      => 'Author',
        'contributor' => 'Contributor',
        'subscriber'  => 'Subscriber',
    ],

    // Roles that may access the admin panel (/admin).
    'staff_roles' => ['super-admin', 'admin', 'editor', 'author', 'contributor'],

    /*
    |--------------------------------------------------------------------------
    | Media conversions used across the CMS
    |--------------------------------------------------------------------------
    */
    'images' => [
        'conversions' => [
            'thumb'  => ['width' => 320,  'height' => 200],
            'medium' => ['width' => 768,  'height' => 480],
            'large'  => ['width' => 1600, 'height' => 900],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reading experience
    |--------------------------------------------------------------------------
    */
    'posts_per_page'   => 12,
    'related_posts'    => 5,
    'words_per_minute' => 200, // for reading-time estimates

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */
    'payments' => [
        'currency'        => env('PAYMENT_CURRENCY', 'BDT'),
        'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'sslcommerz'),
        'gateways'        => ['sslcommerz', 'bkash'],
    ],
];
