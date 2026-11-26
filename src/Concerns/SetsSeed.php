<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Concerns;

use Faker\Generator;

trait SetsSeed
{
    public function setSeed(int $seed): void
    {
        mt_srand($seed);

        if (property_exists($this, 'faker') && $this->faker instanceof Generator) {
            $this->faker->seed($seed);
        }
    }
}
