<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Rezkonline\LaravelFaviconExtractor\Exception\FaviconCouldNotBeSavedException;
use Rezkonline\LaravelFaviconExtractor\Exception\InvalidUrlException;
use Rezkonline\LaravelFaviconExtractor\Favicon\FaviconFactoryInterface;
use Rezkonline\LaravelFaviconExtractor\Favicon\FaviconInterface;
use Rezkonline\LaravelFaviconExtractor\Generator\FilenameGeneratorInterface;
use Rezkonline\LaravelFaviconExtractor\Provider\ProviderInterface;

class FaviconExtractor implements FaviconExtractorInterface
{
    private $faviconFactory;
    private $provider;
    private $filenameGenerator;
    private $url;
    private $favicon;

    public function __construct(FaviconFactoryInterface $faviconFactory, ProviderInterface $provider, FilenameGeneratorInterface $filenameGenerator)
    {
        $this->provider = $provider;
        $this->faviconFactory = $faviconFactory;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function fromUrl(string $url): FaviconExtractorInterface
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function fetchOnly(): FaviconInterface
    {
        $this->favicon = $this->faviconFactory->create(
            $this->provider->fetchFromUrl($this->getUrl())
        );

        return $this->favicon;
    }

    public function fetchAndSaveTo(string $path, string $filename = null): string
    {
        if (null === $filename) {
            $filename = $this->filenameGenerator->generate(16);
        }

        $favicon = $this->fetchOnly();
        $targetPath = $this->getTargetPath($path, $filename);

        if (!Storage::put($targetPath, $favicon->getContent())) {
            throw new FaviconCouldNotBeSavedException(sprintf(
                'The favicon of %s could not be saved at path "%s" ',
                $this->getUrl(), $targetPath
            ));
        }

        return Str::replaceFirst('public/', '', $targetPath);
    }

    private function getTargetPath(string $path, string $filename): string
    {
        return $path.DIRECTORY_SEPARATOR.$filename.'.png';
    }
}
