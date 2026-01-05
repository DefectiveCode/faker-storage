<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Tests\Workers;

use DefectiveCode\Faker\Faker;
use League\Flysystem\Filesystem;
use DefectiveCode\Faker\NameGenerator;
use DefectiveCode\Faker\Generators\Text;
use League\Flysystem\Local\LocalFilesystemAdapter;

trait InitializesWorker
{
    private string $outputDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outputDir = __DIR__.'/../Output';
        $this->cleanOutputDirectory();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanOutputDirectory();
    }

    protected function createFaker(int $count, string $basePath = '', ?int $coroutines = null): Faker
    {
        $faker = Faker::make(Text::class)
            ->toDisk(new Filesystem(new LocalFilesystemAdapter($this->outputDir)))
            ->nameGenerator(NameGenerator::sequence('txt'))
            ->count($count)
            ->seed(1)
            ->sentences(1, 1)
            ->paragraphs(1, 1);

        if ($basePath) {
            $faker->basePath($basePath);
        }

        if ($coroutines !== null) {
            $faker->concurrency($faker->generator->config->threads ?? 1, $coroutines);
        }

        return $faker;
    }

    private function getFiles(string $subPath = ''): array
    {
        $path = $subPath ? $this->outputDir.'/'.$subPath : $this->outputDir;
        $files = glob($path.'/*.txt');
        sort($files);

        return $files;
    }

    private function cleanOutputDirectory(): void
    {
        $this->removeFilesInDirectory($this->outputDir);
    }

    private function removeFilesInDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (array_diff(scandir($directory), ['.', '..', '.gitignore']) as $filename) {
            $filepath = $directory.'/'.$filename;

            if (is_dir($filepath)) {
                $this->removeFilesInDirectory($filepath);
                rmdir($filepath);

                continue;
            }

            unlink($filepath);
        }
    }
}
