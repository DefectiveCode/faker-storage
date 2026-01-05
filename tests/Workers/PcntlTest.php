<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Workers;

use DefectiveCode\Faker\Workers\Pcntl;
use PHPUnit\Framework\Attributes\Test;
use DefectiveCode\Faker\Tests\TestCase;

class PcntlTest extends TestCase
{
    use InitializesWorker;

    #[Test]
    public function itRunsTheCorrectNumberOfJobs(): void
    {
        $this->runFaker(5, 2);

        $this->assertCount(5, $this->getFiles());
    }

    #[Test]
    public function itGeneratesFilesWithCorrectSeeds(): void
    {
        $this->runFaker(3, 1);

        $files = $this->getFiles();
        $this->assertCount(3, $files);

        // Verify each file has deterministic content based on seed
        $hashes = array_map(fn ($file) => sha1_file($file), $files);

        $this->assertEquals('d8632d50f07b4018043ced9cd6ecb279285e1d28', $hashes[0]);
        $this->assertEquals('387530acdfa698be42e8e36219a950c56fc595cf', $hashes[1]);
        $this->assertEquals('60d04e8aa4b8be5a189d83f46fb63a7914628bdd', $hashes[2]);
    }

    #[Test]
    public function itHandlesSingleThreadedExecution(): void
    {
        $this->runFaker(3, 1);

        $this->assertCount(3, $this->getFiles());
    }

    #[Test]
    public function itHandlesMultipleThreads(): void
    {
        $this->runFaker(10, 4);

        $this->assertCount(10, $this->getFiles());
    }

    #[Test]
    public function itGeneratesFilesWithCorrectBasePath(): void
    {
        $this->runFaker(3, 2, 'subfolder');

        $this->assertCount(3, $this->getFiles('subfolder'));
    }

    #[Test]
    public function itGeneratesFilesWithDeterministicNames(): void
    {
        $this->runFaker(3, 1);

        $files1 = $this->getFiles();

        // Clean up
        array_map('unlink', $files1);

        // Run again with same seed
        $this->runFaker(3, 1);

        $files2 = $this->getFiles();

        // Should generate same filenames
        $this->assertEquals(
            array_map('basename', $files1),
            array_map('basename', $files2)
        );
    }

    private function runFaker(int $count, int $concurrency, string $basePath = ''): void
    {
        $faker = $this->createFaker($count, $basePath);
        $faker->concurrency($concurrency);

        (new Pcntl)->run($faker);
    }
}
