<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\Configs\Config;

interface Generator
{
    public function __construct(Config $config);

    public static function getDefaultConfig(): Config;

    /**
     * @return resource
     */
    public function generate(): mixed;

    public function setSeed(int $seed): void;
}
