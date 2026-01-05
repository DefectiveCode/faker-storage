<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\Binary;

class BinaryTest extends TestCase
{
    #[Test]
    public function itSetsTheLength(): void
    {
        $generator = $this->getGenerator();

        $generator->length(512, 1024);

        $this->assertEquals(512, $generator->config->minLengthInBytes);
        $this->assertEquals(1024, $generator->config->maxLengthInBytes);
    }

    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Binary::getDefaultConfig();

        $this->assertEquals('application/octet-stream', $config->contentType);
        $this->assertEquals(NameGenerator::default('bin'), $config->nameGenerator);
    }

    #[Test]
    public function itGeneratesRandomData(): void
    {
        $generator = $this->getGenerator();

        $generator->length(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);

        $this->assertEquals(10, strlen($contents));
        $this->assertEquals(hex2bin('eb488985c08147fc1914'), $contents);
    }

    protected function getGenerator(): Binary
    {
        $config = Binary::getDefaultConfig();

        $generator = new Binary($config);
        $generator->setSeed(1);

        return $generator;
    }
}
