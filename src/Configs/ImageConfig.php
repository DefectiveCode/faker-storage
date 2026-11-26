<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Configs;

class ImageConfig extends Config
{
    public int $gridSize = 0;

    /** @var positive-int */
    public int $minHeight = 100;

    /** @var positive-int */
    public int $maxHeight = 250;

    /** @var positive-int */
    public int $minWidth = 100;

    /** @var positive-int */
    public int $maxWidth = 250;
}
