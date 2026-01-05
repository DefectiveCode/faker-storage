<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Configs;

use Closure;
use League\Flysystem\Filesystem;

abstract class Config
{
    public string $basePath = '/';

    public ?int $threads = null;

    public ?int $coroutines = null;

    public int $count = 1;

    public string $contentType = 'application/octet-stream';

    public array $diskOptions = [];

    public Filesystem $filesystem;

    public Closure $nameGenerator;

    public int $seed = 0;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if (! property_exists($this, $key)) {
                throw new \InvalidArgumentException(
                    sprintf('Unknown configuration option: `%s` on `%s`', $key, static::class)
                );
            }

            $this->seed = random_int(0, PHP_INT_MAX);
            $this->$key = $value;
        }
    }
}
