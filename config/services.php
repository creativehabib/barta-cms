<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
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

    /*
    |--------------------------------------------------------------------------
    | AI assistance (OpenAI-compatible endpoint)
    |--------------------------------------------------------------------------
    | Works with OpenAI, OpenRouter, Groq, Together, or a local Ollama server
    | that speaks the OpenAI chat-completions API.
    */
    'ai' => [
        'enabled'  => (bool) env('AI_ENABLED', false),
        'driver'   => env('AI_DRIVER', 'openai'),
        'base_url' => rtrim((string) env('AI_BASE_URL', 'https://api.openai.com/v1'), '/'),
        'api_key'  => env('AI_API_KEY'),
        'model'    => env('AI_MODEL', 'gpt-4o-mini'),
        'timeout'  => (int) env('AI_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bangladeshi payment gateways
    |--------------------------------------------------------------------------
    */
    'sslcommerz' => [
        'store_id'       => env('SSLCZ_STORE_ID'),
        'store_password' => env('SSLCZ_STORE_PASSWORD'),
        'sandbox'        => (bool) env('SSLCZ_SANDBOX', true),
    ],

    'bkash' => [
        'app_key'    => env('BKASH_APP_KEY'),
        'app_secret' => env('BKASH_APP_SECRET'),
        'username'   => env('BKASH_USERNAME'),
        'password'   => env('BKASH_PASSWORD'),
        'sandbox'    => (bool) env('BKASH_SANDBOX', true),
    ],

];
