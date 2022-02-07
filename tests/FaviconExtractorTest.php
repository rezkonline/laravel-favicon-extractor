<?php

declare(strict_types=1);

namespace Rezkonline\LaravelFaviconExtractor;

use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;
use Rezkonline\LaravelFaviconExtractor\Exception\FaviconCouldNotBeSavedException;
use Rezkonline\LaravelFaviconExtractor\Exception\InvalidUrlException;
use Rezkonline\LaravelFaviconExtractor\Favicon\Favicon;
use Rezkonline\LaravelFaviconExtractor\Favicon\FaviconFactoryInterface;
use Rezkonline\LaravelFaviconExtractor\Generator\FilenameGeneratorInterface;
use Rezkonline\LaravelFaviconExtractor\Provider\ProviderInterface;

class FaviconExtractorTest extends TestCase
{
    /**
     * @var FaviconFactoryInterface|MockInterface
     */
    private $faviconFactory;

    /**
     * @var ProviderInterface|MockInterface
     */
    private $provider;

    /**
     * @var FilenameGeneratorInterface|MockInterface
     */
    private $filenameGenerator;

    /**
     * @var FaviconExtractor
     */
    private $extractor;

    protected function setUp()
    {
        $this->faviconFactory = \Mockery::mock(FaviconFactoryInterface::class);
        $this->provider = \Mockery::mock(ProviderInterface::class);
        $this->filenameGenerator = \Mockery::mock(FilenameGeneratorInterface::class);

        $this->extractor = new FaviconExtractor($this->faviconFactory, $this->provider, $this->filenameGenerator);

        parent::setUp();
    }

    public function test_it_fetches_the_favicon()
    {
        $expectedUrl = 'http://example.com';
        $expectedContent = 'example-content';

        $this->provider
            ->shouldReceive('fetchFromUrl')
            ->once()
            ->with($expectedUrl)
            ->andReturn($expectedContent)
        ;

        $this->faviconFactory
            ->shouldReceive('create')
            ->once()
            ->with($expectedContent)
        ;

        $this->extractor->fromUrl($expectedUrl)->fetchOnly();
    }

    public function test_it_generates_a_filename_if_none_given()
    {
        $this->provider->shouldIgnoreMissing();

        $expectedFavicon = new Favicon('content');

        $this->faviconFactory
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($expectedFavicon)
        ;

        $this->filenameGenerator
            ->shouldReceive('generate')
            ->once()
            ->with(16)
            ->andReturn('random-filename')
        ;

        $this->extractor
            ->fromUrl('http://example.com')
            ->fetchAndSaveTo('some-path')
        ;
    }

    public function test_it_saves_it_properly()
    {
        $this->provider->shouldIgnoreMissing();

        $expectedFavicon = new Favicon('content');

        $this->faviconFactory
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($expectedFavicon)
        ;

        Storage::fake();
        Storage::
            shouldReceive('put')
            ->once()
            ->with('some-path/a-filename.png', 'content')
            ->andReturn(true)
        ;

        $this->extractor
            ->fromUrl('http://example.com')
            ->fetchAndSaveTo('some-path', 'a-filename')
        ;
    }

    public function test_it_throws_an_exception_when_saving_was_not_successful()
    {
        $this->provider->shouldIgnoreMissing();

        $expectedFavicon = new Favicon('content');

        $this->faviconFactory
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($expectedFavicon)
        ;

        Storage::fake();
        Storage::
        shouldReceive('put')
            ->once()
            ->andReturn(false)
        ;

        $this->expectException(FaviconCouldNotBeSavedException::class);

        $this->extractor
            ->fromUrl('http://example.com')
            ->fetchAndSaveTo('some-path', 'a-filename')
        ;
    }
}
