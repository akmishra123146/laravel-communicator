<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active Modules
    |--------------------------------------------------------------------------
    |
    | Here you can specify which modules of the communication suite you want to
    | enable. By default, all modules might be disabled, so you need to
    | turn them on here.
    |
    */
    'modules' => [
        'otp' => false,
        'mail' => false,
        'notification' => false,
        'sms' => false,
        'push' => false,
        'whatsapp' => false,
        'template' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Drivers
    |--------------------------------------------------------------------------
    |
    | Configure the default drivers for various communication modules.
    |
    */
    'defaults' => [
        'sms' => env('COMMUNICATION_SMS_DRIVER', 'twilio'),
        'whatsapp' => env('COMMUNICATION_WHATSAPP_DRIVER', 'meta'),
        'push' => env('COMMUNICATION_PUSH_DRIVER', 'firebase'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Configurations
    |--------------------------------------------------------------------------
    |
    | Configuration options specific to each enabled module.
    |
    */
    
    'otp' => [
        'table' => 'communication_otps',
        'length' => 6,
        'expiry' => 10, // minutes
        'attempt_limit' => 5,
        'resend_cooldown' => 60, // seconds
        'hash' => true,
        'encrypt' => true,
        'default_channel' => 'mail',
    ],

    'sms' => [
        'drivers' => [
            'twilio' => [
                'sid' => env('TWILIO_SID'),
                'token' => env('TWILIO_TOKEN'),
                'from' => env('TWILIO_FROM'),
            ],
            // other drivers...
        ],
    ],
    
    // other modules config...
];
