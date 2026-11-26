<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Workers;

use DefectiveCode\Faker\Faker;
use DefectiveCode\Faker\Generators\Generator;

interface Worker
{
    /**
     * @param  Faker<covariant Generator>  $faker
     */
    public function run(Faker $faker): void;
}
