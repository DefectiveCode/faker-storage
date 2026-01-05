<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\ImageWithAlphaConfig;

class Webp extends AlphaImage
{
    public static function getDefaultConfig(): ImageWithAlphaConfig
    {
        return new ImageWithAlphaConfig([
            'contentType' => 'image/webp',
            'nameGenerator' => NameGenerator::default('webp'),
        ]);
    }

    protected function imageCreator(): string
    {
        return 'imagewebp';
    }
}
