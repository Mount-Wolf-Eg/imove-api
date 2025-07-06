<?php

return [
    'mode' => env('HYPERPAY_MODE', 'test'),
    'entity_id' => env('HYPERPAY_ENTITY_ID'),
    'auth_token' => env('HYPERPAY_AUTH_TOKEN'),
    'merchant_id' => env('HYPERPAY_MERCHANT_ID'),
    'checkout_url' => env('HYPERPAY_CHECKOUT_URL', 'https://eu-test.oppwa.com/v1/checkouts'),
    'payment_status_url' => env('HYPERPAY_PAYMENT_STATUS_URL', 'https://eu-test.oppwa.com/v1/checkouts'),
    'webhook_secret_key' => env('HYPERPAY_WEBHOOK_SECRET_KEY', ''), // Optional, used for webhook validation

];
