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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'alerts' => [
        'slack_webhook_url' => env('SLACK_WEBHOOK_URL'),
        'email_to' => env('ALERTS_EMAIL_TO'),
    ],

    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
        'auth_url' => env('MPESA_AUTH_URL', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'),
        'stk_url' => env('MPESA_STK_URL', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'),
        'webhook_secret' => env('MPESA_WEBHOOK_SECRET'),
        'webhook_ips' => env('MPESA_WEBHOOK_IPS', ''),
    ],

    'webrtc' => [
        'stun_urls' => explode(',', (string) env('WEBRTC_STUN_URLS', 'stun:stun.l.google.com:19302')),
        'turn_urls' => explode(',', (string) env('WEBRTC_TURN_URLS', '')),
        'turn_username' => env('WEBRTC_TURN_USERNAME'),
        'turn_credential' => env('WEBRTC_TURN_CREDENTIAL'),
        'max_participants' => (int) env('WEBRTC_MAX_PARTICIPANTS', 50),
    ],

    'janus' => [
        'url' => env('JANUS_URL', 'http://janus:8088/janus'), // Internal Docker URL
        'ws_url' => env('JANUS_WS_URL', 'ws://localhost:8088/janus'), // External URL for browsers
        'admin_url' => env('JANUS_ADMIN_URL', 'http://janus:8188/admin'),
        'secret' => env('JANUS_SECRET', 'janusoverlord'),
        'admin_secret' => env('JANUS_ADMIN_SECRET', 'janusoverlord'),
    ],

    'sms' => [
        'provider' => env('SMS_PROVIDER', 'africas_talking'), // africas_talking, twilio, etc.
        'api_key' => env('SMS_API_KEY'),
        'username' => env('SMS_USERNAME'),
        'shortcode' => env('SMS_SHORTCODE'),
    ],

    'transcription' => [
        'enabled' => env('TRANSCRIPTION_ENABLED', false),
        'provider' => env('TRANSCRIPTION_PROVIDER', 'whisper'), // whisper, google
        'api_key' => env('TRANSCRIPTION_API_KEY'),
        'whisper_url' => env('TRANSCRIPTION_WHISPER_URL', 'https://api.openai.com/v1/audio/transcriptions'),
        'default_language' => env('TRANSCRIPTION_DEFAULT_LANGUAGE', 'en'),
    ],

];
