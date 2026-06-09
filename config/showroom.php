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

    'design' => [
        'palettes' => [
            'emerald' => [
                'name' => 'Emerald (Default)',
                'primary' => '#10b981',
                'primary_light' => '#34d399',
                'primary_dark' => '#059669',
            ],
            'cobalt' => [
                'name' => 'Cobalt',
                'primary' => '#2563eb',
                'primary_light' => '#60a5fa',
                'primary_dark' => '#1d4ed8',
            ],
            'obsidian' => [
                'name' => 'Obsidian',
                'primary' => '#18181b',
                'primary_light' => '#3f3f46',
                'primary_dark' => '#09090b',
            ],
            'violet' => [
                'name' => 'Violet',
                'primary' => '#7c3aed',
                'primary_light' => '#a78bfa',
                'primary_dark' => '#5b21b6',
            ],
            'rose' => [
                'name' => 'Rose',
                'primary' => '#e11d48',
                'primary_light' => '#fb7185',
                'primary_dark' => '#be123c',
            ],
        ],
        'layouts' => [
            'cinema' => [
                'name' => 'The Cinema',
                'description' => 'Visual-heavy parallax focus.',
            ],
            'bmw_m' => [
                'name' => 'BMW M-Performance',
                'description' => 'Motorsport-engineered interface with black canvas and bold typography.',
            ],
            'nintendo_2001' => [
                'name' => 'Nintendo 2001',
                'description' => 'A brushed-periwinkle "console chrome" interface inspired by 2001 hardware aesthetics.',
            ],
        ],
    ],
];
