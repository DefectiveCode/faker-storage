<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Bmp;
use DefectiveCode\Faker\Tests\TestCase;

class BmpTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Bmp::getDefaultConfig();

        $this->assertEquals('image/bmp', $config->contentType);
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
        $this->assertStringStartsWith('BM', $contents);
    }

    protected function getGenerator(): Bmp
    {
        $config = Bmp::getDefaultConfig();

        $generator = new Bmp($config);
        $generator->setSeed(1);

        return $generator;
    }
}
