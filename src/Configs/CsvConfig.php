<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Configs;

class CsvConfig extends Config
{
    public string $delimiter = ',';

    public string $enclosure = '"';

    public string $escape = '';

    public string $eol = "\n";

    public int $minColumns = 5;

    public int $maxColumns = 50;

    public int $minRows = 100;

    public int $maxRows = 5000;
}
