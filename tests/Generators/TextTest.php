<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\Text;

class TextTest extends TestCase
{
    #[Test]
    public function itSetsTheParagraphLength(): void
    {
        $generator = $this->getGenerator();

        $generator->paragraphs(1, 2);

        $this->assertEquals(1, $generator->config->minParagraphs);
        $this->assertEquals(2, $generator->config->maxParagraphs);
    }

    #[Test]
    public function itSetsTheSentenceLength(): void
    {
        $generator = $this->getGenerator();

        $generator->sentences(1, 2);

        $this->assertEquals(1, $generator->config->minSentences);
        $this->assertEquals(2, $generator->config->maxSentences);
    }

    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Text::getDefaultConfig();

        $this->assertEquals('text/plain', $config->contentType);
    }

    #[Test]
    public function itGeneratesRandomData(): void
    {
        $generator = $this->getGenerator();

        $generator->sentences(1, 1);
        $generator->paragraphs(1, 1);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $this->assertEquals("Rerum quaerat ut fuga non quibusdam itaque ut. At quia quibusdam commodi.\n", stream_get_contents($data));
    }

    protected function getGenerator(): Text
    {
        $config = Text::getDefaultConfig();

        $generator = new Text($config);
        $generator->prepare();
        $generator->setSeed(1);

        return $generator;
    }
}
