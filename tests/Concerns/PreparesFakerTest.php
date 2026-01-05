<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Concerns;

use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Concerns\PreparesFaker;

class PreparesFakerTest extends TestCase
{
    #[Test]
    public function itPreparesFaker(): void
    {
        $class = new class
        {
            use PreparesFaker;

            public function getFaker(): Generator
            {
                return $this->faker;
            }
        };

        $class->prepare();

        $this->assertInstanceOf(Generator::class, $class->getFaker());
    }
}
