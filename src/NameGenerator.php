<?php

declare(strict_types=1);

namespace DefectiveCode\Faker;

use Closure;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;
use DefectiveCode\Faker\Generators\Generator;

class NameGenerator
{
    protected static string $currentGenerator = 'uuid';

    public static function default(string $extension): Closure
    {
        $currentGenerator = self::$currentGenerator;

        if (! method_exists(self::class, $currentGenerator)) {
            throw new InvalidArgumentException("Method `{$currentGenerator}` does not exist");
        }

        return self::$currentGenerator($extension);
    }

    public static function setDefault(string $generator): void
    {
        if (! method_exists(self::class, $generator)) {
            throw new InvalidArgumentException("Method `{$generator}` does not exist");
        }

        self::$currentGenerator = $generator;
    }

    public static function sequence(string $extension): Closure
    {
        return fn (int $seed, int $completedFiles, Generator $generator) => $completedFiles.'.'.$extension;
    }

    public static function uuid(string $extension): Closure
    {
        return fn (int $seed, int $completedFiles, Generator $generator) => Uuid::uuid4()->toString().'.'.$extension;
    }
}
