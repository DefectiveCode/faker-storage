<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Concerns;

use Faker\Factory;
use Faker\Generator;

trait PreparesFaker
{
    protected Generator $faker;

    public function prepare(): void
    {
        $this->faker = Factory::create();
    }
}
