<?php

return [
    'enabled' => env('BD_NOTIFICATIONS_ENABLED', true),

    'company_email' => env('BD_NOTIFICATION_EMAIL', env('MAIL_CONTACT_TO', 'info@bdiversity.co.mz')),
    'company_name' => env('BD_NOTIFICATION_NAME', 'Business Diversity'),

    'reply_to' => [
        'address' => env('MAIL_REPLY_TO_ADDRESS', env('MAIL_CONTACT_TO', 'info@bdiversity.co.mz')),
        'name' => env('MAIL_REPLY_TO_NAME', env('MAIL_FROM_NAME', 'Business Diversity')),
    ],
];
