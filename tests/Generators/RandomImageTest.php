<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Generators;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use DefectiveCode\Faker\Generators\RandomImage;

class RandomImageTest extends TestCase
{
    #[Test]
    public function itReturnsTheDefaultConfig(): void
    {
        $config = RandomImage::getDefaultConfig();

        $this->assertEquals('image/random', $config->contentType);
    }

    /**
     * @param  list<string>  $contentTypes
     */
    #[Test]
    #[TestWith([false, ['image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp']])]
    #[TestWith([true, ['image/avif', 'image/gif', 'image/png', 'image/webp']])]
    public function itGeneratesAnImageInTheWonFormat(bool $withAlpha, array $contentTypes): void
    {
        $generator = $this->getGenerator();

        $generator->withAlpha($withAlpha);
        $generator->height(10, 10);
        $generator->width(10, 10);

        $data = $generator->generate();

        rewind($data);

        $contents = (string) stream_get_contents($data);
        $contentType = $generator->config->contentType;

        $this->assertContains($contentType, $contentTypes);
        $this->assertSame($contentType, getimagesizefromstring($contents)['mime'] ?? null);
        $this->assertStringEndsWith(
            '.'.substr($contentType, strlen('image/')),
            ($generator->config->nameGenerator)(1, 0, $generator)
        );
    }

    protected function getGenerator(): RandomImage
    {
        $config = RandomImage::getDefaultConfig();

        $generator = new RandomImage($config);
        $generator->setSeed(1);

        return $generator;
    }
}
