<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Gif;
use DefectiveCode\Faker\Tests\TestCase;

class GifTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Gif::getDefaultConfig();

        $this->assertEquals('image/gif', $config->contentType);
        $this->assertEquals(NameGenerator::default('gif'), $config->nameGenerator);
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
        $this->assertStringStartsWith('GIF87a', $contents);
    }

    protected function getGenerator(): Gif
    {
        $config = Gif::getDefaultConfig();

        $generator = new Gif($config);
        $generator->setSeed(1);

        return $generator;
    }
}
