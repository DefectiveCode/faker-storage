<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\ImageWithAlphaConfig;

class Gif extends AlphaImage
{
    public static function getDefaultConfig(): ImageWithAlphaConfig
    {
        return new ImageWithAlphaConfig([
            'contentType' => 'image/gif',
            'nameGenerator' => NameGenerator::default('gif'),
        ]);
    }

    protected function imageCreator(): string
    {
        return 'imagegif';
    }
}
