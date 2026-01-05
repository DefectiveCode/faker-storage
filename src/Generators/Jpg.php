<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\ImageConfig;

class Jpg extends Image
{
    public static function getDefaultConfig(): ImageConfig
    {
        return new ImageConfig([
            'contentType' => 'image/jpeg',
            'nameGenerator' => NameGenerator::default('jpg'),
        ]);
    }

    protected function imageCreator(): string
    {
        return 'imagejpeg';
    }
}
