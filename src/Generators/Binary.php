<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Generators;

use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Configs\Config;
use DefectiveCode\Faker\Concerns\SetsSeed;
use DefectiveCode\Faker\Configs\BinaryConfig;

class Binary implements Generator
{
    use SetsSeed;

    /**
     * @param  BinaryConfig  $config
     */
    public function __construct(public Config $config) {}

    public static function getDefaultConfig(): Config
    {
        return new BinaryConfig([
            'contentType' => 'application/octet-stream',
            'nameGenerator' => NameGenerator::default('bin'),
        ]);
    }

    public function generate(): mixed
    {
        $stream = fopen('php://temp', 'w+');

        for ($i = 0; $i < mt_rand($this->config->minLengthInBytes, $this->config->maxLengthInBytes); $i++) {
            fwrite($stream, chr(mt_rand(0, 255)));
        }

        return $stream;
    }

    public function length(int $minLengthInBytes = 1024, int $maxLengthInBytes = 10240): self
    {
        $this->config->minLengthInBytes = $minLengthInBytes;
        $this->config->maxLengthInBytes = $maxLengthInBytes;

        return $this;
    }
}
