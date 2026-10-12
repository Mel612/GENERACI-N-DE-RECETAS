<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'gemini' => [
    'key'    => env('GEMINI_API_KEY'),
    'modelo' => env('GEMINI_MODELO', 'gemini-3.8-flash'),
    // Modelo alterno por si el principal está saturado (opcional)
    'respaldo' => env('GEMINI_MODELO_RESPALDO'),
    'url'    => env('GEMINI_URL', 'https://generativelanguage.googleapis.com/v1beta/interactions'),
    'limite_diario' => env('GEMINI_LIMITE_DIARIO', 10),
],
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
