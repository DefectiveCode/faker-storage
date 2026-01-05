<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Concerns\SetsSeed;
use DefectiveCode\Faker\Configs\TextConfig;
use DefectiveCode\Faker\Concerns\PreparesFaker;

class Text implements Generator
{
    use PreparesFaker;
    use SetsSeed;

    /**
     * @param  TextConfig  $config
     */
    public function __construct(public Config $config) {}

    public function paragraphs(int $minParagraphs, int $maxParagraphs): self
    {
        $this->config->minParagraphs = $minParagraphs;
        $this->config->maxParagraphs = $maxParagraphs;

        return $this;
    }

    public function sentences(int $minSentences, int $maxSentences): self
    {
        $this->config->minSentences = $minSentences;
        $this->config->maxSentences = $maxSentences;

        return $this;
    }

    public static function getDefaultConfig(): TextConfig
    {
        return new TextConfig([
            'contentType' => 'text/plain',
            'nameGenerator' => NameGenerator::default('txt'),
        ]);
    }

    public function generate(): mixed
    {
        $stream = fopen('php://temp', 'w+');

        for ($paragraphs = 0; $paragraphs < mt_rand($this->config->minParagraphs, $this->config->maxParagraphs); $paragraphs++) {
            $paragraph = $this->faker->paragraph(mt_rand($this->config->minSentences, $this->config->maxSentences)).PHP_EOL;
            fwrite($stream, $paragraph);
        }

        return $stream;
    }
}
