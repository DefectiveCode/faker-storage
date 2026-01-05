<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\ImageConfig;

class Bmp extends Image
{
    public static function getDefaultConfig(): ImageConfig
    {
        return new ImageConfig([
            'contentType' => 'image/bmp',
            'nameGenerator' => NameGenerator::default('bmp'),
        ]);
    }

    protected function imageCreator(): string
    {
        return 'imagebmp';
    }
}
