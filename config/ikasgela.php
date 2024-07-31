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

    'pagination_assigned_activities' => intval(env('PAGINATION_ASSIGNED_ACTIVITIES', 20)),
    'pagination_available_activities' => intval(env('PAGINATION_AVAILABLE_ACTIVITIES', 10)),

    'pdf_report_enabled' => env('PDF_REPORT_ENABLED', false),
    'excel_report_enabled' => env('EXCEL_REPORT_ENABLED', false),

    'jplag_delete_temp' => env('JPLAG_DELETE_TEMP', true),

    'avatar_enabled' => env('AVATAR_ENABLED', false),
];
