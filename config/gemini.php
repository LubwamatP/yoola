<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Gemini AI integration for Yoola.ug
    | AI-powered search functionality.
    |
    */

    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    |
    | The Gemini API endpoint. Using gemini-2.0-flash for optimal performance
    | and cost-effectiveness.
    |
    */

    'endpoint' => env('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent'),

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    */

    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait for Gemini API response.
    | Keep low for search to maintain good UX.
    |
    */

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    */

    'search' => [
        // Enable/disable AI-powered search
        'enabled' => env('GEMINI_SEARCH_ENABLED', true),

        // Minimum confidence score to use AI results (0-1)
        'min_confidence' => env('GEMINI_MIN_CONFIDENCE', 0.7),

        // Cache TTL for search results in seconds (1 hour default)
        'cache_ttl' => env('GEMINI_SEARCH_CACHE_TTL', 3600),

        // Maximum products to analyze per search
        'max_products' => env('GEMINI_SEARCH_MAX_PRODUCTS', 100),

        // Enable conversational context (multi-turn)
        'conversational' => env('GEMINI_CONVERSATIONAL', true),

        // Context window for conversational search (in minutes)
        'context_window' => env('GEMINI_CONTEXT_WINDOW', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Gemini API rate limiting configuration.
    | Free tier: 60 RPM, Standard: 1500 RPM
    |
    */

    'rate_limit' => [
        'requests_per_minute' => env('GEMINI_RATE_LIMIT_RPM', 60),
        'requests_per_day' => env('GEMINI_RATE_LIMIT_RPD', 1500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for when Gemini API fails or is unavailable.
    |
    */

    'fallback' => [
        // Enable fallback to traditional search
        'enabled' => true,

        // Show user notification when falling back
        'notify_user' => false,

        // Log API failures
        'log_failures' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | Languages supported for search queries.
    | Phase 1: English, Luganda
    |
    */

    'languages' => [
        'en' => 'English',
        'lg' => 'Luganda',
        'sw' => 'Swahili',
    ],

    /*
    |--------------------------------------------------------------------------
    | Uganda-specific Configuration
    |--------------------------------------------------------------------------
    |
    | Localization settings for the Ugandan market.
    |
    */

    'localization' => [
        'currency' => 'UGX',
        'currency_symbol' => 'UGX',
        'default_language' => 'en',
    ],

];
