<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\RandomImage;

class RandomImageTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = RandomImage::getDefaultConfig();

        $this->assertEquals('image/random', $config->contentType);
    }

    #[Test]
    public function itSetsContentTypeBasedOnWonFormat(): void
    {
        $generator = $this->getGenerator();

        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);
        $this->assertContains($generator->config->contentType, [
            'image/avif',
            'image/bmp',
            'image/gif',
            'image/jpeg',
            'image/png',
            'image/webp',
        ]);
    }

    #[Test]
    public function itSetsNameGeneratorBasedOnWonFormat(): void
    {
        $generator = $this->getGenerator();

        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);
    }

    #[Test]
    public function itSetsContentTypeBasedOnWonFormatWithAlpha(): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha(true);
        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);
        $this->assertContains($generator->config->contentType, [
            'image/avif',
            'image/gif',
            'image/png',
            'image/webp',
        ]);
    }

    #[Test]
    public function itSetsNameGeneratorBasedOnWonFormatWithAlpha(): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha(true);
        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);
    }

    protected function getGenerator(): RandomImage
    {
        $config = RandomImage::getDefaultConfig();

        $generator = new RandomImage($config);
        $generator->setSeed(1);

        return $generator;
    }
}
