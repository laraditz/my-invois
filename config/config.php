<?php

/*
 * You can place your configuration in here.
 */
return [
    'base_url' => 'https://api.myinvois.hasil.gov.my',
    'middleware' => ['api'],
    'client_id' => env('MYINVOIS_CLIENT_ID'),
    'client_secret' => env('MYINVOIS_CLIENT_SECRET'),
    'sandbox' => [
        'mode' => env('MYINVOIS_SANDBOX', false),
        'base_url' => 'https://preprod-api.myinvois.hasil.gov.my',
    ],
    'routes' => [
        'prefix' => 'my-invois',
        'auth' => [
            'token' => 'POST /connect/token',
        ],
    ]
];