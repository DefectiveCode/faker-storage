<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Configs;

class ImageConfig extends Config
{
    public int $gridSize = 0;

    public int $minHeight = 100;

    public int $maxHeight = 250;

    public int $minWidth = 100;

    public int $maxWidth = 250;
}
