<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor\Facades;

use Illuminate\Support\Facades\Facade;

class FaviconExtractor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'favicon.extractor';
    }
}
