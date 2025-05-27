<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPhpSets()
    ->withAttributesSets()
    ->withParallel(600, 16, 10)
    ->withImportNames()
    ->withSkip([
        __DIR__ . '/vendor',
        __DIR__ . '/node_modules',
        MigrateToSimplifiedAttributeRector::class,
    ])
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_COLLECTION,
    ]);
