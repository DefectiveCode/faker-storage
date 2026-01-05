<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\ImageWithAlphaConfig;

class Png extends AlphaImage
{
    public static function getDefaultConfig(): ImageWithAlphaConfig
    {
        return new ImageWithAlphaConfig([
            'contentType' => 'image/png',
            'nameGenerator' => NameGenerator::default('png'),
        ]);
    }

    protected function imageCreator(): string
    {
        return 'imagepng';
    }
}
