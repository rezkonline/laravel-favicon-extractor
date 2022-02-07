<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor;

use Illuminate\Support\ServiceProvider;
use Rezkonline\LaravelFaviconExtractor\Favicon\FaviconFactory;
use Rezkonline\LaravelFaviconExtractor\Favicon\FaviconFactoryInterface;
use Rezkonline\LaravelFaviconExtractor\Generator\FilenameGeneratorInterface;
use Rezkonline\LaravelFaviconExtractor\Provider\ProviderInterface;

class FaviconExtractorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/favicon-extractor.php' => config_path('favicon-extractor.php')
        ], 'config');

        $this->app->bind(
            FaviconFactoryInterface::class,
            FaviconFactory::class
        );

        $this->app->bind(
            ProviderInterface::class,
            config('favicon-extractor.provider_class')
        );

        $this->app->bind(
            FilenameGeneratorInterface::class,
            config('favicon-extractor.filename_generator_class')
        );

        $this->app->bind(
            FaviconExtractorInterface::class,
            FaviconExtractor::class
        );

        $this->app->alias(FaviconExtractorInterface::class, 'favicon.extractor');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/favicon-extractor.php', 'favicon-extractor');
    }
}
