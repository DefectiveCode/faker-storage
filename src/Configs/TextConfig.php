<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Configs;

class TextConfig extends Config
{
    public int $minParagraphs = 100;

    public int $maxParagraphs = 1000;

    public int $minSentences = 3;

    public int $maxSentences = 20;
}
