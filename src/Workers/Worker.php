<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Workers;

use DefectiveCode\Faker\Faker;

interface Worker
{
    public function run(Faker $faker);
}
