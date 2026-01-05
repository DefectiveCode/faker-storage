<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Concerns\SetsSeed;
use DefectiveCode\Faker\Configs\CsvConfig;
use DefectiveCode\Faker\Concerns\PreparesFaker;

class Csv implements Generator
{
    use PreparesFaker;
    use SetsSeed;

    /**
     * @param  CsvConfig  $config
     */
    public function __construct(public Config $config) {}

    public function delimiter(string $delimiter = ','): self
    {
        $this->config->delimiter = $delimiter;

        return $this;
    }

    public function enclosure(string $enclosure = '"'): self
    {
        $this->config->enclosure = $enclosure;

        return $this;
    }

    public function escape(string $escape = ''): self
    {
        $this->config->escape = $escape;

        return $this;
    }

    public function eol(string $eol = "\n"): self
    {
        $this->config->eol = $eol;

        return $this;
    }

    public function columns(int $minColumns = 5, int $maxColumns = 50): self
    {
        $this->config->minColumns = $minColumns;
        $this->config->maxColumns = $maxColumns;

        return $this;
    }

    public function rows(int $minRows = 100, int $maxRows = 500): self
    {
        $this->config->minRows = $minRows;
        $this->config->maxRows = $maxRows;

        return $this;
    }

    public static function getDefaultConfig(): CsvConfig
    {
        return new CsvConfig([
            'contentType' => 'text/csv',
            'nameGenerator' => NameGenerator::default('csv'),
        ]);
    }

    public function generate(): mixed
    {
        $headerCount = mt_rand($this->config->minColumns, $this->config->maxColumns);

        $stream = fopen('php://temp', 'w+');
        $this->writeRow($stream, $this->faker->unique()->words($headerCount));

        for ($rows = 0; $rows < mt_rand($this->config->minRows, $this->config->maxRows); $rows++) {
            $this->writeRow($stream, $this->faker->words($headerCount));
        }

        return $stream;
    }

    /**
     * @param  resource  $stream
     */
    protected function writeRow($stream, array $fields): void
    {
        fputcsv(
            $stream,
            $fields,
            $this->config->delimiter,
            $this->config->enclosure,
            $this->config->escape,
            $this->config->eol
        );
    }
}
