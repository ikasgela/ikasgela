<?php

return [

    'repo_cache_days' => env('REPO_CACHE_DAYS', 7),
    'markdown_cache_days' => env('MARKDOWN_CACHE_DAYS', 7),
    'eloquent_cache_time' => env('ELOQUENT_CACHE_TIME', 7200),

    'gitlab_enabled' => env('GITLAB_ENABLED', false),
    'gitea_enabled' => env('GITEA_ENABLED', false),

    'version' => env('VERSION', '0.0'),
    'commit' => env('COMMIT', env('APP_ENV', 'unknown')),

    'tinymce_apikey' => env('TINYMCE_APIKEY', 'none'),

    'message_preview_max_length' => env('MESSAGE_PREVIEW_MAX_LENGTH', 300),

];
