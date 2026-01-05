<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\Webp;

class WebpTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Webp::getDefaultConfig();

        $this->assertEquals('image/webp', $config->contentType);
        $this->assertEquals(NameGenerator::default('webp'), $config->nameGenerator);
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
        $this->assertStringStartsWith('RIFF', $contents);
        $this->assertStringContainsString('WEBP', $contents);
    }

    protected function getGenerator(): Webp
    {
        $config = Webp::getDefaultConfig();

        $generator = new Webp($config);
        $generator->setSeed(1);

        return $generator;
    }
}
