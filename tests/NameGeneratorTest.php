<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests;

use InvalidArgumentException;
use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Text;

class NameGeneratorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        NameGenerator::setDefault('uuid');
    }

    #[Test]
    public function sequenceGeneratesSequentialFilenames(): void
    {
        $generator = NameGenerator::sequence('txt');
        $textGenerator = new Text(Text::getDefaultConfig());

        $this->assertEquals('0.txt', $generator(1, 0, $textGenerator));
        $this->assertEquals('1.txt', $generator(1, 1, $textGenerator));
        $this->assertEquals('2.txt', $generator(1, 2, $textGenerator));
        $this->assertEquals('99.txt', $generator(1, 99, $textGenerator));
    }

    #[Test]
    public function uuidGeneratesValidUuidFilenames(): void
    {
        $generator = NameGenerator::uuid('jpg');
        $textGenerator = new Text(Text::getDefaultConfig());

        $filename = $generator(1, 0, $textGenerator);

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\.jpg$/', $filename);
    }

    #[Test]
    public function defaultReturnsUuidGenerator(): void
    {
        $generator = NameGenerator::default('txt');
        $textGenerator = new Text(Text::getDefaultConfig());

        $filename = $generator(1, 0, $textGenerator);

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\.txt$/', $filename);
    }

    #[Test]
    public function setDefaultChangesDefaultGenerator(): void
    {
        NameGenerator::setDefault('sequence');

        $generator = NameGenerator::default('txt');
        $textGenerator = new Text(Text::getDefaultConfig());

        $this->assertEquals('0.txt', $generator(1, 0, $textGenerator));
        $this->assertEquals('1.txt', $generator(1, 1, $textGenerator));
    }

    #[Test]
    public function setDefaultThrowsExceptionForInvalidGenerator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method `invalid` does not exist');

        NameGenerator::setDefault('invalid');
    }

    #[Test]
    public function defaultThrowsExceptionWhenDefaultIsInvalid(): void
    {
        $reflection = new \ReflectionClass(NameGenerator::class);
        $property = $reflection->getProperty('currentGenerator');
        $property->setAccessible(true);
        $property->setValue(null, 'invalid');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method `invalid` does not exist');

        NameGenerator::default('txt');
    }
}
