<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\Avif;

class AvifTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Avif::getDefaultConfig();

        $this->assertEquals('image/avif', $config->contentType);
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
        $this->assertStringContainsString('ftypavif', $contents);
    }

    protected function getGenerator(): Avif
    {
        $config = Avif::getDefaultConfig();

        $generator = new Avif($config);
        $generator->setSeed(1);

        return $generator;
    }
}
