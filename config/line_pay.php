<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Line Pay Provider Name
    |--------------------------------------------------------------------------
    | sandbox / production
    */

    'provider' => env('LINE_PAY_PROVIDER', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Line Pay Configuration
    |--------------------------------------------------------------------------
    */
    'options' => [
        'channel_id'                 => env('LINE_PAY_CHANNEL_ID', ''),
        'channel_secret'             => env('LINE_PAY_CHANNEL_SECRET', ''),
        'merchant_device_profile_id' => env('LINE_PAY_MERCHANT_DEVICE_PROFILE_ID', ''),
        'create_response'            => \Rober\LinePay\Service\Response::class,
    ],
];
