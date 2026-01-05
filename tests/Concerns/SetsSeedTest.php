<?php

declare(strict_types=1);

namespace Concerns;

use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;
use DefectiveCode\Faker\Concerns\SetsSeed;
use DefectiveCode\Faker\Concerns\PreparesFaker;

class SetsSeedTest extends TestCase
{
    #[Test]
    public function itSetsTheSeed(): void
    {
        $class = new class
        {
            use SetsSeed;
        };

        $class->setSeed(1);

        $this->assertSame(895547922, mt_rand());
    }

    #[Test]
    public function itSetsTheFakerSeedIfFakerExists(): void
    {
        $class = new class
        {
            use PreparesFaker;
            use SetsSeed;

            public function word(): string
            {
                return $this->faker->word();
            }
        };

        $class->prepare();
        $class->setSeed(1);

        $this->assertEquals(895547922, mt_rand());
        $this->assertEquals('dolor', $class->word());
    }
}
