<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Workers;

use Swoole\Atomic;
use Swoole\Runtime;
use Swoole\Coroutine;
use Swoole\Process\Pool;
use DefectiveCode\Faker\Faker;
use Swoole\Coroutine\WaitGroup;

class Swoole implements Worker
{
    public function run(Faker $faker): void
    {
        $completedFiles = new Atomic(0);
        $finishedWorkers = new Atomic(0);
        $totalFiles = $faker->generator->config->count;
        $threads = min($faker->generator->config->threads ?? swoole_cpu_num(), $totalFiles);

        $pool = new Pool(
            $threads,
            enable_coroutine: true
        );

        $pool->on('WorkerStart', function ($pool) use ($completedFiles, $totalFiles, $finishedWorkers, $threads, $faker): void {
            Runtime::enableCoroutine();

            $totalCoroutines = $faker->generator->config->coroutines ?? $threads * 4;
            $coroutinesPerWorker = (int) ceil($totalCoroutines / $threads);
            $waitGroup = new WaitGroup;

            for ($runningCoroutines = 0; $runningCoroutines < $coroutinesPerWorker; $runningCoroutines++) {
                $waitGroup->add();

                Coroutine::create(function () use ($waitGroup, $completedFiles, $totalFiles, $faker): void {
                    while (($fileIndex = $completedFiles->add() - 1) < $totalFiles) {
                        $faker->job()($faker->getSeed($fileIndex), $fileIndex);
                    }

                    $waitGroup->done();
                });
            }

            $waitGroup->wait();

            if ($finishedWorkers->add() === $threads) {
                $pool->shutdown();
            }
        });

        $pool->start();
    }
}
