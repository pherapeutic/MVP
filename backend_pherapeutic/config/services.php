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
        'therapist_push_notification_key' => env('THERAPIST_PUSH_NOTIFICATION_KEY', 'AAAASRWrOZ0:APA91bGBUc7Ydf0Xp3tYG47ROCZw-6KtuMao593xfIBRatphi4D-V1_GRAN56s9M_LfykPGcV0dox80RSdwoMmBjANpoLOPJ5x3YrUt6mo_OVbx6MDkTc7C0OPiTG0ExSCwVi_knq61u'),
    ],

    'agora' => [
        'app_id' => env('AGORA_APP_ID','0d97119f3b6744d58af674a7abdd76d1'),
        'app_certificate' => env('AGORA_APP_CERTIFICATE','a6ab0d27cb114f65b3c5cb7942b26cf4'),
    ],

];
