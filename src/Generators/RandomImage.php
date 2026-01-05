<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Configs\ImageWithAlphaConfig;

class RandomImage extends AlphaImage
{
    /** @var string[] */
    public static array $alphaImages = [
        'avif',
        'gif',
        'png',
        'webp',
    ];

    /** @var string[] */
    public static array $images = [
        'avif',
        'bmp',
        'gif',
        'jpeg',
        'png',
        'webp',
    ];

    protected string $wonFormat;

    public static function getDefaultConfig(): Config
    {
        return new ImageWithAlphaConfig([
            'contentType' => 'image/random',
            'nameGenerator' => NameGenerator::default('random'),
        ]);
    }

    protected function imageCreator(): string
    {
        return "image{$this->wonFormat}";
    }

    public function generate(): mixed
    {
        $imageArray = $this->config->withAlpha ? self::$alphaImages : self::$images;
        $this->wonFormat = $imageArray[array_rand($imageArray)];

        $this->config->contentType = "image/{$this->wonFormat}";
        $this->config->nameGenerator = NameGenerator::default($this->wonFormat);

        return parent::generate();
    }
}
