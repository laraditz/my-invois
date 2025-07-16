<?php

/*
 * You can place your configuration in here.
 */
return [
    'base_url' => 'https://api.myinvois.hasil.gov.my',
    'middleware' => ['api'],
    'client_id' => env('MYINVOIS_CLIENT_ID'),
    'client_secret' => env('MYINVOIS_CLIENT_SECRET'),
    'on_behalf_of' => env('MYINVOIS_ON_BEHALF_OF', null),
    'sandbox' => [
        'mode' => env('MYINVOIS_SANDBOX', false),
        'base_url' => 'https://preprod-api.myinvois.hasil.gov.my',
    ],
    'disk' => env('MYINVOIS_DISK', 'local'),
    'document_path' => env('MYINVOIS_DOCUMENT_PATH', 'myinvois/'),
    'certificate_path' => env('MYINVOIS_CERTIFICATE_PATH', storage_path('app/myinvois.p12')),
    'private_key_path' => env('MYINVOIS_PRIVATE_KEY_PATH', storage_path('app/myinvois.pem')),
    'passphrase' => env('MYINVOIS_PASSPHRASE'),
    'routes' => [
        'prefix' => 'my-invois',
        'auth' => [
            'token' => 'POST /connect/token',
        ],
        'document_type' => [
            'all' => '/api/v1.0/documenttypes',
            'get' => '/api/v1.0/documenttypes/{id}',
            'version' => '/api/v1.0/documenttypes/{id}/versions/{vid}',
        ],
        'notification' => [
            'all' => '/api/v1.0/notifications/taxpayer',
        ],
        'taxpayer' => [
            'validate_tin' => '/api/v1.0/taxpayer/validate/{tin}',
            'search_tin' => '/api/v1.0/taxpayer/search/tin'
        ],
        'document' => [
            'submit' => 'POST /api/v1.0/documentsubmissions',
            'recent' => '/api/v1.0/documents/recent',
            'search' => '/api/v1.0/documents/search',
            'get' => '/api/v1.0/documents/{uuid}/raw',
            'details' => '/api/v1.0/documents/{uuid}/details',
            'cancel' => 'PUT /api/v1.0/documents/state/{uuid}/state',
            'reject' => 'PUT /api/v1.0/documents/state/{uuid}/state',
        ],
        'document_submission' => [
            'get' => '/api/v1.0/documentsubmissions/{submissionUid}',
        ],
    ]
];