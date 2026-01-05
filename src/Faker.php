<?php

declare(strict_types=1);

namespace DefectiveCode\Faker;

use Closure;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use DefectiveCode\Faker\Workers\Pcntl;
use DefectiveCode\Faker\Workers\Swoole;
use DefectiveCode\Faker\Generators\Generator;

class Faker
{
    public function __construct(public Generator $generator) {}

    public function __call(string $name, array $arguments): self
    {
        if (! method_exists($this->generator, $name)) {
            throw new InvalidArgumentException("Method $name does not exist.");
        }

        call_user_func([$this->generator, $name], ...$arguments);

        return $this;
    }

    /**
     * @template G of Generator
     *
     * @param  class-string<G>  $generator
     * @return Faker<G>&G
     */
    public static function make(string $generator): self
    {
        if (! is_subclass_of($generator, Generator::class)) {
            throw new InvalidArgumentException("`{$generator}` must implement the Generator interface.");
        }

        return new self(new $generator($generator::getDefaultConfig()));
    }

    public function basePath(string $basePath): self
    {
        $this->generator->config->basePath = $basePath;

        return $this;
    }

    public function toDisk(Filesystem $filesystem): self
    {
        $this->generator->config->filesystem = $filesystem;

        return $this;
    }

    public function diskOptions(array $diskOptions): self
    {
        $this->generator->config->diskOptions = $diskOptions;

        return $this;
    }

    public function concurrency(int $threads, ?int $coroutines = null): self
    {
        $this->generator->config->threads = $threads;
        $this->generator->config->coroutines = $coroutines;

        return $this;
    }

    public function count(int $count): self
    {
        $this->generator->config->count = $count;

        return $this;
    }

    /**
     * @param  Closure(int $seed, int $completedFiles, Generator $generator): string  $generator
     */
    public function nameGenerator(Closure $generator): self
    {
        $this->generator->config->nameGenerator = $generator;

        return $this;
    }

    public function seed(int $seed): self
    {
        $this->generator->config->seed = $seed;

        return $this;
    }

    public function generate(): void
    {
        extension_loaded('swoole')
            ? new Swoole()->run($this)
            : new Pcntl()->run($this);
    }

    public function getSeed(int $completedFiles): int
    {
        $seed = sha1($this->generator->config->seed.':'.$completedFiles, true);

        return unpack('Pseed', substr($seed, 0, 8))['seed'];
    }

    public function job(): Closure
    {
        return function (int $seed, int $completedFiles): void {
            if (method_exists($this->generator, 'prepare')) {
                $this->generator->prepare();
            }

            $this->generator->setSeed($seed);
            $stream = $this->generator->generate();

            rewind($stream);

            $this->filesystem()->writeStream(
                $this->generator->config->basePath.'/'.call_user_func($this->generator->config->nameGenerator, $seed, $completedFiles, $this->generator),
                $stream,
                [
                    'mimetype' => $this->generator->config->contentType,
                    ...$this->generator->config->diskOptions,
                ]
            );

            if (is_resource($stream)) {
                fclose($stream);
            }
        };
    }

    protected function filesystem(): Filesystem
    {
        if (! $this->generator->config->filesystem) {
            throw new InvalidArgumentException('No disk configuration provided. Call `toDisk` before generating files.');
        }

        return $this->generator->config->filesystem;
    }
}
