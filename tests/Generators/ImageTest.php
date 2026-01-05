<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Generators\Image;
use DefectiveCode\Faker\Configs\ImageConfig;

class ImageTest extends TestCase
{
    #[Test]
    public function itSetsTheGridSize(): void
    {
        $generator = $this->getGenerator();

        $generator->grid(5);

        $this->assertEquals(5, $generator->config->gridSize);
    }

    #[Test]
    public function itSetsTheHeight(): void
    {
        $generator = $this->getGenerator();

        $generator->height(100, 200);

        $this->assertEquals(100, $generator->config->minHeight);
        $this->assertEquals(200, $generator->config->maxHeight);
    }

    #[Test]
    public function itSetsTheWidth(): void
    {
        $generator = $this->getGenerator();

        $generator->width(150, 300);

        $this->assertEquals(150, $generator->config->minWidth);
        $this->assertEquals(300, $generator->config->maxWidth);
    }

    #[Test]
    public function itGeneratesRandomImage(): void
    {
        $generator = $this->getGenerator();

        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);
        $this->assertStringStartsWith("\x89PNG", $contents);
        $this->assertEquals('421e0977ee84fc24284047a293da9380728054ad', sha1($contents));
    }

    #[Test]
    public function itGeneratesGridImage(): void
    {
        $generator = $this->getGenerator();

        $generator->height(100, 100);
        $generator->width(100, 100);
        $generator->grid(5);

        $data = $generator->generate();

        $this->assertIsResource($data);

        rewind($data);

        $contents = stream_get_contents($data);
        $this->assertStringStartsWith("\x89PNG", $contents);
        $this->assertEquals('956025127c463915344918da0843c85987958ac5', sha1($contents));
    }

    protected function getGenerator(): Image
    {
        $config = new ImageConfig([
            'contentType' => 'image/test',
        ]);

        $generator = new class($config) extends Image
        {
            public static function getDefaultConfig(): ImageConfig
            {
                return new ImageConfig([
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
