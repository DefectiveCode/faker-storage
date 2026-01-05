<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Jpg;
use DefectiveCode\Faker\Tests\TestCase;

class JpgTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Jpg::getDefaultConfig();

        $this->assertEquals('image/jpeg', $config->contentType);
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
        $this->assertStringStartsWith("\xFF\xD8\xFF", $contents);
    }

    protected function getGenerator(): Jpg
    {
        $config = Jpg::getDefaultConfig();

        $generator = new Jpg($config);
        $generator->setSeed(1);

        return $generator;
    }
}
