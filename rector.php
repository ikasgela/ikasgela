<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withParallel(600, 16, 10)
    ->withImportNames()
    ->withSkip([
        __DIR__ . '/vendor',
        __DIR__ . '/node_modules',
    ])
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
    ]);
