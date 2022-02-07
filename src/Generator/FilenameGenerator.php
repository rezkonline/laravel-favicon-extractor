<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor\Generator;

class FilenameGenerator implements FilenameGeneratorInterface
{
    public function generate(int $length): string
    {
        return str_random($length);
    }
}
