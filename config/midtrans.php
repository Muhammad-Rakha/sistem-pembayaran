<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    | Credentials are loaded from environment variables — never hard-coded.
    | Keep MIDTRANS_SERVER_KEY server-side only. Never log or expose it.
    */

    'server_key'       => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'       => env('MIDTRANS_CLIENT_KEY', ''),
    'merchant_id'      => env('MIDTRANS_MERCHANT_ID', ''),
    'is_production'    => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'     => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds'           => env('MIDTRANS_IS_3DS', true),
    // Public HTTPS URL for Midtrans to POST notifications.
    // In local dev: set this to your ngrok/cloudflared tunnel URL.
    // Example: https://xxxx.ngrok-free.app/webhooks/midtrans
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Local Simulator
    |--------------------------------------------------------------------------
    | The simulator is automatically disabled when is_production = true.
    */
    'simulator_enabled' => env('SIMULATOR_ENABLED', true),
    'simulator_secret'  => env('SIMULATOR_WEBHOOK_SECRET', 'change_this_secret'),

    /*
    |--------------------------------------------------------------------------
    | VA Bank Options (for the payment form)
    |--------------------------------------------------------------------------
    */
    'banks' => ['bca', 'bni', 'bri', 'permata'],
];
