<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
    ])
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
    ])
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
