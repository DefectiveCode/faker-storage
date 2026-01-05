<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Png;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\Email;

class EmailTest extends TestCase
{
    #[Test]
    public function itSetsTheParagraphs(): void
    {
        $generator = $this->getGenerator();

        $generator->paragraphs(5, 10);

        $this->assertEquals(5, $generator->config->minParagraphs);
        $this->assertEquals(10, $generator->config->maxParagraphs);
    }

    #[Test]
    public function itSetsTheSentences(): void
    {
        $generator = $this->getGenerator();

        $generator->sentences(2, 8);

        $this->assertEquals(2, $generator->config->minSentences);
        $this->assertEquals(8, $generator->config->maxSentences);
    }

    #[Test]
    public function itSetsTheAttachmentsWithClassName(): void
    {
        $generator = $this->getGenerator();

        $generator->withAttachment(Png::class, 1, 3);

        $this->assertEquals(Png::class, $generator->config->attachmentGenerator);
        $this->assertEquals(1, $generator->config->minAttachments);
        $this->assertEquals(3, $generator->config->maxAttachments);
    }

    #[Test]
    public function itSetsTheAttachmentsWithGeneratorInstance(): void
    {
        $generator = $this->getGenerator();
        $pngGenerator = new Png(Png::getDefaultConfig());

        $generator->withAttachment($pngGenerator, 2, 4);

        $this->assertSame($pngGenerator, $generator->config->attachmentGenerator);
        $this->assertEquals(2, $generator->config->minAttachments);
        $this->assertEquals(4, $generator->config->maxAttachments);
    }

    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = Email::getDefaultConfig();

        $this->assertEquals('message/rfc822', $config->contentType);
        $this->assertEquals(NameGenerator::default('eml'), $config->nameGenerator);
    }

    #[Test]
    public function itGeneratesRandomData(): void
    {
        $generator = $this->getGenerator();

        $generator->paragraphs(3, 3);
        $generator->sentences(2, 2);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);

        $this->assertMatchesRegularExpression('/^To: .+$/m', $contents);
        $this->assertMatchesRegularExpression('/^From: .+$/m', $contents);
        $this->assertMatchesRegularExpression('/^Subject: .+$/m', $contents);
        $this->assertStringContainsString('Message-ID: <', $contents);
        $this->assertMatchesRegularExpression('/^Date: .+$/m', $contents);

        $bodyStart = strpos($contents, "\r\n\r\n");
        $body = substr($contents, $bodyStart + 4);
        $body = quoted_printable_decode($body);
        $paragraphs = array_filter(explode("\r\n\r\n", trim($body)));

        $this->assertCount(3, $paragraphs);
    }

    #[Test]
    public function itGeneratesEmailWithAttachments(): void
    {
        $generator = $this->getGenerator();

        $generator->paragraphs(1, 1);
        $generator->sentences(1, 1);
        $generator->withAttachment(Png::class, 1, 1);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);

        $this->assertStringContainsString('Content-Type: multipart/mixed', $contents);
        $this->assertStringContainsString('Content-Type: image/png', $contents);
        $this->assertStringContainsString('Content-Disposition: attachment', $contents);
    }

    #[Test]
    public function itGeneratesDeterministicMessageId(): void
    {
        $generator = $this->getGenerator();
        $generator->paragraphs(1, 1);
        $generator->sentences(1, 1);

        $data = $generator->generate();
        rewind($data);
        $contents = stream_get_contents($data);

        $this->assertStringContainsString('Message-ID: <3e92e5c2b0d632b3a36fbbb17484b7fe@', $contents);
    }

    protected function getGenerator(): Email
    {
        $config = Email::getDefaultConfig();

        $generator = new Email($config);
        $generator->prepare();
        $generator->setSeed(1);

        return $generator;
    }
}
