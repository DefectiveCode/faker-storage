<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Configs;

use DefectiveCode\Faker\Generators\Generator;

class EmailConfig extends Config
{
    public string|Generator $attachmentGenerator;

    public int $minAttachments = 1;

    public int $maxAttachments = 3;

    public int $minParagraphs = 100;

    public int $maxParagraphs = 1000;

    public int $minSentences = 3;

    public int $maxSentences = 20;
}
