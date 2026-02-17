<?php

return [
    'admin_seed_emails' => array_values(
        array_unique(
            array_filter(
                array_map(
                    static fn (string $email): string => strtolower(trim($email)),
                    explode(',', (string) env('ADMIN_SEED_EMAILS', '')),
                ),
                static fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
            ),
        ),
    ),
    'admin_password' => (string) env('ADMIN_PASSWORD', 'password'),
    'employee_defaults' => [
        'job_title' => (string) env('EMPLOYEE_DEFAULT_JOB_TITLE', 'Showroom Staff'),
        'preferred_locale' => (string) env('EMPLOYEE_DEFAULT_LOCALE', 'en_PH'),
        'preferred_timezone' => (string) env('EMPLOYEE_DEFAULT_TIMEZONE', 'Asia/Manila'),
    ],
];
