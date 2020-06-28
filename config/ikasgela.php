<?php

return [

    'repo_cache_days' => env('REPO_CACHE_DAYS', 7),
    'markdown_cache_days' => env('MARKDOWN_CACHE_DAYS', 7),

    'gitlab_enabled' => env('GITLAB_ENABLED', false),
    'gitea_enabled' => env('GITEA_ENABLED', false),

    'version' => env('VERSION', '0.0'),
    'commit' => env('COMMIT', 'local'),

    'tinymce_apikey' => env('TINYMCE_APIKEY', 'none'),

    'message_preview_max_length' => env('MESSAGE_PREVIEW_MAX_LENGTH', 300),

];
