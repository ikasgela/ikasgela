<?php

return [

    'repo_cache_days' => intval(env('REPO_CACHE_DAYS', 7)),
    'markdown_cache_days' => intval(env('MARKDOWN_CACHE_DAYS', 7)),
    'eloquent_cache_time' => intval(env('ELOQUENT_CACHE_TIME', 7200)),

    'gitlab_enabled' => env('GITLAB_ENABLED', false),
    'gitea_enabled' => env('GITEA_ENABLED', false),

    'version' => env('VERSION', '0.0'),
    'commit' => env('COMMIT', env('APP_ENV', 'unknown')),

    'tinymce_apikey' => env('TINYMCE_APIKEY', 'none'),

    'message_preview_max_length' => intval(env('MESSAGE_PREVIEW_MAX_LENGTH', 300)),

    'pagination_short' => intval(env('PAGINATION_SHORT', 10)),
    'pagination_medium' => intval(env('PAGINATION_MEDIUM', 25)),
    'pagination_long' => intval(env('PAGINATION_LONG', 100)),

    'pdf_report_enabled' => env('PDF_REPORT_ENABLED', false),
    'excel_report_enabled' => env('EXCEL_REPORT_ENABLED', false),

    'jplag_delete_temp' => env('JPLAG_DELETE_TEMP', true),

    'avatar_enabled' => env('AVATAR_ENABLED', false),

    'auth_throttle' => [
        'login_max_attempts' => intval(env('AUTH_LOGIN_MAX_ATTEMPTS', 5)),
        'login_decay_minutes' => intval(env('AUTH_LOGIN_DECAY_MINUTES', 1)),
        'register_max_attempts' => intval(env('AUTH_REGISTER_MAX_ATTEMPTS', 10)),
        'register_decay_minutes' => intval(env('AUTH_REGISTER_DECAY_MINUTES', 60)),
        'password_reset_max_attempts' => intval(env('AUTH_PASSWORD_RESET_MAX_ATTEMPTS', 5)),
        'password_reset_decay_minutes' => intval(env('AUTH_PASSWORD_RESET_DECAY_MINUTES', 60)),
        'tinymce_url_max_attempts' => intval(env('AUTH_TINYMCE_URL_MAX_ATTEMPTS', 60)),
        'tinymce_url_decay_minutes' => intval(env('AUTH_TINYMCE_URL_DECAY_MINUTES', 1)),
    ],
];
