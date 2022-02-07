<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor\Provider;

interface ProviderInterface
{
    public function fetchFromUrl(string $url): string;
}
