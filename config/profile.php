<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Profile Photo Configuration
    |--------------------------------------------------------------------------
    |
    | Profile photos are square (1:1 ratio) and generated in multiple sizes
    | for performance optimization.
    |
    */

    'photo' => [
        'max_size' => 2048, // KB (2MB)
        'dimensions' => [
            'large' => 300,    // 300x300px
            'medium' => 150,   // 150x150px
            'small' => 50,     // 50x50px (thumbnails)
        ],
        'ratio' => '1:1', // Square
        'quality' => [
            'large' => 80,
            'medium' => 75,
            'small' => 70,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cover Photo Configuration
    |--------------------------------------------------------------------------
    |
    | Cover photos use 16:9 ratio for social media style headers.
    |
    */

    'cover' => [
        'max_size' => 4096, // KB (4MB)
        'width' => 1200,
        'height' => 400,
        'ratio' => '16:9',
        'quality' => 80,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Photos are stored in the public disk for simplicity.
    | Can be easily changed to S3 or CDN later.
    |
    */

    'storage' => [
        'disk' => 'public',
        'path' => 'profiles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Username Configuration
    |--------------------------------------------------------------------------
    |
    | Usernames must be unique and follow social media conventions.
    |
    */

    'username' => [
        'min' => 3,
        'max' => 20,
        'pattern' => '/^[a-z0-9_]+$/',
        'reserved' => [
            'admin', 'administrator', 'root', 'system',
            'api', 'www', 'mail', 'ftp', 'support',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bio Configuration
    |--------------------------------------------------------------------------
    */

    'bio' => [
        'max_length' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile Completeness Fields
    |--------------------------------------------------------------------------
    |
    | Fields that count towards profile completion percentage.
    |
    */

    'completeness_fields' => [
        'username',
        'bio',
        'profile_photo',
        'country',
        'timezone',
        'experience_level',
        'trading_style',
        'preferred_sessions',
        'favorite_pairs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Assets
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'avatar' => '/images/default-avatar.png',
        'cover' => '/images/default-cover.jpg',
    ],

];
