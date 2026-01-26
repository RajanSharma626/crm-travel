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

    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    */

    'sms' => [
        'api_url' => env('SMS_API_URL', 'http://hostname.in/api/send/sms'),
        'auth_key' => env('SMS_AUTH_KEY', 'YOUR AUTH KEY'),
        'sender' => env('SMS_SENDER', 'ABCXYZ'),
        'route' => env('SMS_ROUTE', 'TR'), // TR for transactional, PR for promotional

        'templates' => [
            'welcome' => [
                'name' => 'Welcome Message',
                'message' => 'Hi {{customer_name}}, thank you for contacting us! We are excited to help you plan your trip to {{destination}}. Our team will get back to you shortly.',
                'template_id' => env('SMS_TEMPLATE_WELCOME', '1234567890123456789'),
                'campaign_name' => 'Welcome SMS',
                'coding' => 1,
                'description' => 'Welcome message for new leads',
            ],

            'follow_up' => [
                'name' => 'Follow Up',
                'message' => 'Hi {{customer_name}}, just checking in regarding your travel plans to {{destination}}. Our team is ready to assist you. Please feel free to reach out!',
                'template_id' => env('SMS_TEMPLATE_FOLLOWUP', '1234567890123456789'),
                'campaign_name' => 'Follow Up SMS',
                'coding' => 1,
                'description' => 'Follow-up message for existing leads',
            ],

            'quote_ready' => [
                'name' => 'Quote Ready',
                'message' => 'Hi {{customer_name}}, your customized quote for {{destination}} is ready! Please check your email or contact us for details. Travel date: {{travel_date}}',
                'template_id' => env('SMS_TEMPLATE_QUOTE', '1234567890123456789'),
                'campaign_name' => 'Quote Ready SMS',
                'coding' => 1,
                'description' => 'Notification when quote is ready',
            ],

            'booking_confirmation' => [
                'name' => 'Booking Confirmation',
                'message' => 'Hi {{customer_name}}, your booking for {{destination}} has been confirmed! Travel date: {{travel_date}}. We will send you the details shortly.',
                'template_id' => env('SMS_TEMPLATE_BOOKING', '1234567890123456789'),
                'campaign_name' => 'Booking Confirmation SMS',
                'coding' => 1,
                'description' => 'Booking confirmation message',
            ],

            'payment_reminder' => [
                'name' => 'Payment Reminder',
                'message' => 'Hi {{customer_name}}, this is a friendly reminder about the pending payment for your {{destination}} trip. Please contact us for payment details.',
                'template_id' => env('SMS_TEMPLATE_PAYMENT', '1234567890123456789'),
                'campaign_name' => 'Payment Reminder SMS',
                'coding' => 1,
                'description' => 'Payment reminder message',
            ],

            'travel_reminder' => [
                'name' => 'Travel Reminder',
                'message' => 'Hi {{customer_name}}, your trip to {{destination}} is coming up on {{travel_date}}! Have a wonderful journey. Contact us if you need any assistance.',
                'template_id' => env('SMS_TEMPLATE_TRAVEL', '1234567890123456789'),
                'campaign_name' => 'Travel Reminder SMS',
                'coding' => 1,
                'description' => 'Travel date reminder',
            ],

            'thank_you' => [
                'name' => 'Thank You',
                'message' => 'Hi {{customer_name}}, thank you for choosing us for your {{destination}} trip! We hope you had a wonderful experience. Please share your feedback with us.',
                'template_id' => env('SMS_TEMPLATE_THANKYOU', '1234567890123456789'),
                'campaign_name' => 'Thank You SMS',
                'coding' => 1,
                'description' => 'Thank you message after trip',
            ],
        ],
    ],

];
