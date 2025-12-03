<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google API Configuration
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        'service_account_path' => env('GOOGLE_SERVICE_ACCOUNT_PATH', storage_path('app/google-service-account.json')),
        'scopes' => [
            'https://www.googleapis.com/auth/indexing',
            'https://www.googleapis.com/auth/webmasters.readonly',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IndexNow Configuration
    |--------------------------------------------------------------------------
    */
    'indexnow' => [
        'enabled' => env('INDEXNOW_ENABLED', true),
        'api_key_length' => env('INDEXNOW_API_KEY_LENGTH', 32),
        'endpoints' => [
            'bing' => 'https://api.indexnow.org/IndexNow',
            'yandex' => 'https://yandex.com/indexnow',
            'naver' => 'https://searchadvisor.naver.com/indexnow',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Indexing Configuration
    |--------------------------------------------------------------------------
    */
    'auto_indexing' => [
        'enabled' => env('AUTO_INDEXING_ENABLED', false),
        'schedule' => env('AUTO_INDEXING_SCHEDULE', 'daily'), // daily, hourly, etc.
        'check_new_pages_interval' => env('CHECK_NEW_PAGES_INTERVAL', 24), // hours
        'max_pages_per_batch' => env('MAX_PAGES_PER_BATCH', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'connection' => env('PAGE_INDEXER_QUEUE_CONNECTION', 'default'),
        'queue' => env('PAGE_INDEXER_QUEUE', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'google' => [
            'max_per_day' => env('GOOGLE_MAX_INDEXING_PER_DAY', 200),
            'max_per_minute' => env('GOOGLE_MAX_INDEXING_PER_MINUTE', 10),
        ],
        'indexnow' => [
            'max_per_day' => env('INDEXNOW_MAX_PER_DAY', 10000),
            'max_per_minute' => env('INDEXNOW_MAX_PER_MINUTE', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('PAGE_INDEXER_CACHE_ENABLED', true),
        'ttl' => env('PAGE_INDEXER_CACHE_TTL', 3600), // 1 hour
    ],
];

