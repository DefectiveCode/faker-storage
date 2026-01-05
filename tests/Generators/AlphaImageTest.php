<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\AlphaImage;
use DefectiveCode\Faker\Configs\ImageWithAlphaConfig;

class AlphaImageTest extends TestCase
{
    #[Test]
    public function itSetsTheAlpha(): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha(true);

        $this->assertTrue($generator->config->withAlpha);
    }

    #[Test]
    public function itSetsTheAlphaToFalse(): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha(false);

        $this->assertFalse($generator->config->withAlpha);
    }

    #[Test]
    public function itGeneratesImageWithAlpha(): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha(true);
        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);
        $this->assertStringStartsWith("\x89PNG", $contents);
        $this->assertEquals('064c9afca2d82145cb51ae943c31ef6f8640bcce', sha1($contents));
    }

    #[Test]
    public function itGeneratesImageWithoutAlpha(): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha(false);
        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);
        $this->assertStringStartsWith("\x89PNG", $contents);
        $this->assertEquals('421e0977ee84fc24284047a293da9380728054ad', sha1($contents));
    }

    protected function getGenerator(): AlphaImage
    {
        $config = new ImageWithAlphaConfig([
            'contentType' => 'image/test',
        ]);

        $generator = new class($config) extends AlphaImage
        {
            public static function getDefaultConfig(): ImageWithAlphaConfig
            {
                return new ImageWithAlphaConfig([
                    'contentType' => 'image/test',
                ]);
            }

            protected function imageCreator(): string
            {
                return 'imagepng';
            }
        };

        $generator->setSeed(1);

        return $generator;
    }
}
