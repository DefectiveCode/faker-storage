<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Configs;

class BinaryConfig extends Config
{
    public int $minLengthInBytes = 1024;

    public int $maxLengthInBytes = 10240;
}
