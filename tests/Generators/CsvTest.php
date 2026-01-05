<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Csv;
use DefectiveCode\Faker\Tests\TestCase;

class CsvTest extends TestCase
{
    #[Test]
    public function itSetsTheDelimiter(): void
    {
        $generator = $this->getGenerator();

        $generator->delimiter(';');

        $this->assertEquals(';', $generator->config->delimiter);
    }

    #[Test]
    public function itSetsTheEnclosure(): void
    {
        $generator = $this->getGenerator();

        $generator->enclosure("'");

        $this->assertEquals("'", $generator->config->enclosure);
    }

    #[Test]
    public function itSetsTheEscape(): void
    {
        $generator = $this->getGenerator();

        $generator->escape('\\');

        $this->assertEquals('\\', $generator->config->escape);
    }

    #[Test]
    public function itSetsTheEol(): void
    {
        $generator = $this->getGenerator();

        $generator->eol("\r\n");

        $this->assertEquals("\r\n", $generator->config->eol);
    }

    #[Test]
    public function itSetsTheColumns(): void
    {
        $generator = $this->getGenerator();

        $generator->columns(10, 20);

        $this->assertEquals(10, $generator->config->minColumns);
        $this->assertEquals(20, $generator->config->maxColumns);
    }

    #[Test]
    public function itSetsTheRows(): void
    {
        $generator = $this->getGenerator();

        $generator->rows(50, 100);

        $this->assertEquals(50, $generator->config->minRows);
        $this->assertEquals(100, $generator->config->maxRows);
    }

    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Csv::getDefaultConfig();

        $this->assertEquals('text/csv', $config->contentType);
        $this->assertEquals(NameGenerator::default('csv'), $config->nameGenerator);
    }

    #[Test]
    public function itGeneratesRandomData(): void
    {
        $generator = $this->getGenerator();

        $generator->columns(3, 3);
        $generator->rows(2, 2);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);
        $this->assertEquals('31a875a7d17f7c45c18e5664d1266acccba6a052', sha1($contents));
    }

    protected function getGenerator(): Csv
    {
        $config = Csv::getDefaultConfig();

        $generator = new Csv($config);
        $generator->prepare();
        $generator->setSeed(1);

        return $generator;
    }
}
