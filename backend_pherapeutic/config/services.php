<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'currency' => env('STRIPE_CURRENCY'),
        'amount' => env('AMOUNT'),
        'client_id' => env('STRIPE_CLIENT_ID'),
    ],

    'notification' => [
        'therapist_push_notification_key' => env('THERAPIST_PUSH_NOTIFICATION_KEY', 'AAAAqYvxCrk:APA91bEANxdtQ6kF8207-aoskGNTJbKV7Y5z3rXO3fHFoWHUAXklmUozMb3vgnKYsC1jt0Q_2klXt0L90REfTojIo0Cn9J68h51M2g2-D5rCfXClLET5-MWfvKBpqfU2u3r6sTwmCH9g'),
    ],

    'agora' => [
        'app_id' => env('AGORA_APP_ID'),
        'app_certificate' => env('AGORA_APP_CERTIFICATE'),
    ],

];
