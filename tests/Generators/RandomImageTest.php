<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use DefectiveCode\Faker\NameGenerator;
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
        $this->assertEquals(NameGenerator::default('random'), $config->nameGenerator);
    }

    #[Test]
    public function itGeneratesRandomData(): void
    {
        $generator = $this->getGenerator();

        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);
        $this->assertEquals('f802d117f6af5b4d042eed81b9256153c0db1d18', sha1($contents));
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

        $format = str_replace('image/', '', $generator->config->contentType);
        $this->assertEquals(NameGenerator::default($format), $generator->config->nameGenerator);
    }

    #[Test]
    public function itGeneratesRandomDataWithAlpha(): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha(true);
        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);
        $this->assertEquals('bfa4c86029e3b606e059ec4a2eb9306b15139b70', sha1($contents));
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

        $format = str_replace('image/', '', $generator->config->contentType);
        $this->assertEquals(NameGenerator::default($format), $generator->config->nameGenerator);
    }

    protected function getGenerator(): RandomImage
    {
        $config = RandomImage::getDefaultConfig();

        $generator = new RandomImage($config);
        $generator->setSeed(1);

        return $generator;
    }
}
