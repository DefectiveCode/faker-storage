<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\ImageWithAlphaConfig;

class Avif extends AlphaImage
{
    public static function getDefaultConfig(): ImageWithAlphaConfig
    {
        return new ImageWithAlphaConfig([
            'contentType' => 'image/avif',
            'nameGenerator' => NameGenerator::default('avif'),
        ]);
    }

    protected function imageCreator(): string
    {
        return 'imageavif';
    }
}
