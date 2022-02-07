<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor\Favicon;

interface FaviconInterface
{
    public function getContent(): string;
}
