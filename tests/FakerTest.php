<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests;

use stdClass;
use InvalidArgumentException;
use DefectiveCode\Faker\Faker;
use League\Flysystem\Filesystem;
use DefectiveCode\Faker\NameGenerator;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Generators\Text;
use League\Flysystem\Local\LocalFilesystemAdapter;

class FakerTest extends TestCase
{
    #[Test]
    public function makeCreatesNewFakerInstance(): void
    {
        $faker = Faker::make(Text::class);

        $this->assertInstanceOf(Text::class, $faker->generator);
    }

    #[Test]
    public function makeThrowsExceptionForInvalidGenerator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('`stdClass` must implement the Generator interface.');

        Faker::make(stdClass::class);
    }

    #[Test]
    public function basePathSetsBasePath(): void
    {
        $faker = Faker::make(Text::class)->basePath('test/path');

        $this->assertEquals('test/path', $faker->generator->config->basePath);
    }

    #[Test]
    public function toDiskSetsFilesystem(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(__DIR__));
        $faker = Faker::make(Text::class)->toDisk($filesystem);

        $this->assertSame($filesystem, $faker->generator->config->filesystem);
    }

    #[Test]
    public function diskOptionsSetsDiskOptions(): void
    {
        $options = ['visibility' => 'public'];
        $faker = Faker::make(Text::class)->diskOptions($options);

        $this->assertEquals($options, $faker->generator->config->diskOptions);
    }

    #[Test]
    public function concurrencySetsThreadsAndCoroutines(): void
    {
        $faker = Faker::make(Text::class)->concurrency(4, 16);

        $this->assertEquals(4, $faker->generator->config->threads);
        $this->assertEquals(16, $faker->generator->config->coroutines);
    }

    #[Test]
    public function concurrencySetsThreadsOnly(): void
    {
        $faker = Faker::make(Text::class)->concurrency(4);

        $this->assertEquals(4, $faker->generator->config->threads);
        $this->assertNull($faker->generator->config->coroutines);
    }

    #[Test]
    public function countSetsCount(): void
    {
        $faker = Faker::make(Text::class)->count(10);

        $this->assertEquals(10, $faker->generator->config->count);
    }

    #[Test]
    public function nameGeneratorSetsNameGenerator(): void
    {
        $generator = NameGenerator::sequence('txt');
        $faker = Faker::make(Text::class)->nameGenerator($generator);

        $this->assertSame($generator, $faker->generator->config->nameGenerator);
    }

    #[Test]
    public function nameGeneratorWorksWithCustomClosure(): void
    {
        $customGenerator = fn (int $seed, int $completedFiles, $generator) => "custom_{$completedFiles}.dat";
        $faker = Faker::make(Text::class)->nameGenerator($customGenerator);

        $this->assertSame($customGenerator, $faker->generator->config->nameGenerator);

        $textGenerator = new Text(Text::getDefaultConfig());
        $this->assertEquals('custom_0.dat', $customGenerator(1, 0, $textGenerator));
        $this->assertEquals('custom_5.dat', $customGenerator(1, 5, $textGenerator));
        $this->assertEquals('custom_99.dat', $customGenerator(1, 99, $textGenerator));
    }

    #[Test]
    public function seedSetsSeed(): void
    {
        $faker = Faker::make(Text::class)->seed(42);

        $this->assertEquals(42, $faker->generator->config->seed);
    }

    #[Test]
    public function getSeedGeneratesDeterministicSeed(): void
    {
        $faker = Faker::make(Text::class)->seed(1);

        $seed0 = $faker->getSeed(0);
        $seed1 = $faker->getSeed(1);
        $seed2 = $faker->getSeed(2);

        $this->assertEquals(2665252444718633, $seed0);
        $this->assertEquals(2635236709239322430, $seed1);
        $this->assertEquals(-299242497799967239, $seed2);
    }

    #[Test]
    public function magicCallProxiesToGenerator(): void
    {
        $faker = Faker::make(Text::class)->sentences(5, 10);

        $this->assertEquals(5, $faker->generator->config->minSentences);
        $this->assertEquals(10, $faker->generator->config->maxSentences);
    }

    #[Test]
    public function magicCallThrowsExceptionForInvalidMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method invalid does not exist.');

        Faker::make(Text::class)->invalid();
    }

    #[Test]
    public function jobThrowsErrorWhenNoFilesystemSet(): void
    {
        $faker = Faker::make(Text::class)
            ->seed(1)
            ->nameGenerator(NameGenerator::sequence('txt'));

        $job = $faker->job();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property DefectiveCode\Faker\Configs\Config::$filesystem must not be accessed before initialization');

        $job(1, 0);
    }

    #[Test]
    public function methodChainingWorks(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(__DIR__));

        $faker = Faker::make(Text::class)
            ->toDisk($filesystem)
            ->basePath('test')
            ->count(5)
            ->concurrency(2)
            ->seed(1)
            ->sentences(1, 1)
            ->paragraphs(1, 1)
            ->nameGenerator(NameGenerator::sequence('txt'));

        $this->assertInstanceOf(Faker::class, $faker);
        $this->assertEquals('test', $faker->generator->config->basePath);
        $this->assertEquals(5, $faker->generator->config->count);
        $this->assertEquals(2, $faker->generator->config->threads);
        $this->assertEquals(1, $faker->generator->config->seed);
    }
}
