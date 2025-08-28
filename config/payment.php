<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for various payment gateways
    | used in the LMS platform.
    |
    */

    'default' => env('PAYMENT_DEFAULT', 'stripe'),

    'gateways' => [
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', true),
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'currency' => env('STRIPE_CURRENCY', 'usd'),
            'min_amount' => env('STRIPE_MIN_AMOUNT', 0.50),
        ],

        'paypal' => [
            'enabled' => env('PAYPAL_ENABLED', true),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
            'currency' => env('PAYPAL_CURRENCY', 'USD'),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        ],

        'paymob' => [
            'enabled' => env('PAYMOB_ENABLED', true),
            'api_key' => env('PAYMOB_API_KEY'),
            'integration_id' => env('PAYMOB_INTEGRATION_ID'),
            'iframe_id' => env('PAYMOB_IFRAME_ID'),
            'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
            'currency' => env('PAYMOB_CURRENCY', 'EGP'),
        ],
    ],

    'currencies' => [
        'USD' => [
            'symbol' => '$',
            'position' => 'left',
            'decimals' => 2,
        ],
        'SAR' => [
            'symbol' => 'ر.س',
            'position' => 'right',
            'decimals' => 2,
        ],
        'EGP' => [
            'symbol' => 'ج.م',
            'position' => 'right',
            'decimals' => 2,
        ],
        'AED' => [
            'symbol' => 'د.إ',
            'position' => 'right',
            'decimals' => 2,
        ],
    ],

    'tax' => [
        'enabled' => env('PAYMENT_TAX_ENABLED', true),
        'rate' => env('PAYMENT_TAX_RATE', 15), // Percentage
        'countries' => [
            'SA' => 15, // Saudi Arabia
            'AE' => 5,  // UAE
            'EG' => 14, // Egypt
            'JO' => 16, // Jordan
        ],
    ],

    'webhooks' => [
        'enabled' => env('PAYMENT_WEBHOOKS_ENABLED', true),
        'timeout' => env('PAYMENT_WEBHOOK_TIMEOUT', 30),
        'retry_attempts' => env('PAYMENT_WEBHOOK_RETRY_ATTEMPTS', 3),
    ],

    'security' => [
        'fraud_detection' => env('PAYMENT_FRAUD_DETECTION', true),
        'max_amount' => env('PAYMENT_MAX_AMOUNT', 10000),
        'rate_limiting' => env('PAYMENT_RATE_LIMITING', true),
        'ip_whitelist' => env('PAYMENT_IP_WHITELIST', ''),
    ],

    'notifications' => [
        'email' => [
            'payment_success' => env('PAYMENT_EMAIL_SUCCESS', true),
            'payment_failed' => env('PAYMENT_EMAIL_FAILED', true),
            'payment_refunded' => env('PAYMENT_EMAIL_REFUNDED', true),
        ],
        'sms' => [
            'enabled' => env('PAYMENT_SMS_ENABLED', false),
            'provider' => env('PAYMENT_SMS_PROVIDER', 'twilio'),
        ],
    ],
];
