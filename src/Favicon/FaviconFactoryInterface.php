<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor\Favicon;

interface FaviconFactoryInterface
{
    public function create(string $content): Favicon;
}
