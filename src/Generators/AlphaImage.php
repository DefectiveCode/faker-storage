<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Configs\ImageWithAlphaConfig;

abstract class AlphaImage extends Image
{
    /**
     * @param  ImageWithAlphaConfig  $config
     */
    public function __construct(public Config $config) {}

    public function withAlpha(bool $enabled = true): self
    {
        $this->config->withAlpha = $enabled;

        return $this;
    }
}
