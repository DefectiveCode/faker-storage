<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Png;
use DefectiveCode\Faker\Tests\TestCase;

class PngTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Png::getDefaultConfig();

        $this->assertEquals('image/png', $config->contentType);
        $this->assertEquals(NameGenerator::default('png'), $config->nameGenerator);
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
        $this->assertStringStartsWith("\x89PNG", $contents);
    }

    protected function getGenerator(): Png
    {
        $config = Png::getDefaultConfig();

        $generator = new Png($config);
        $generator->setSeed(1);

        return $generator;
    }
}
