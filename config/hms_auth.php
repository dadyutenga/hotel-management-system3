<?php

return [
    'passkey' => [
        'max_attempts' => (int) env('PASSKEY_MAX_ATTEMPTS', 5),
        'lockout_minutes' => (int) env('PASSKEY_LOCKOUT_MINUTES', 15),
    ],

    'staff_session' => [
        'hours' => (int) env('STAFF_SESSION_HOURS', 8),
    ],

    'property_code_required' => (bool) env('PROPERTY_CODE_REQUIRED', true),
];
