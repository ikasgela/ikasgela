<?php

return [

    'repo_cache_days' => env('REPO_CACHE_DAYS', 7),
    'markdown_cache_days' => env('MARKDOWN_CACHE_DAYS', 7),

    'gitlab_enabled' => env('GITLAB_ENABLED', false),
    'gitea_enabled' => env('GITEA_ENABLED', false),

    'version' => env('VERSION', '0.0'),
    'commit' => env('COMMIT', 'local'),

];
