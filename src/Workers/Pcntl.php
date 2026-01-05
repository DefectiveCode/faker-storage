<?php

declare(strict_types=1);

namespace DefectiveCode\Faker\Workers;

use Exception;
use DefectiveCode\Faker\Faker;

class Pcntl implements Worker
{
    public function run(Faker $faker): void
    {
        pcntl_sigprocmask(SIG_BLOCK, [SIGCHLD]);

        $completedFiles = 0;
        $currentWorkers = 0;
        $maxWorkers = $faker->generator->config->threads;

        $drain = function () use (&$currentWorkers): void {
            while ((pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
                $currentWorkers--;
            }
        };

        while ($completedFiles < $faker->generator->config->count) {
            while ($currentWorkers <= $maxWorkers && $completedFiles < $faker->generator->config->count) {
                $pid = pcntl_fork();

                if ($pid === -1) {
                    throw new Exception('Failed to fork.');
                }

                if ($pid === 0) {
                    $faker->job()($faker->getSeed($completedFiles), $completedFiles);
                    exit(0);
                }

                $currentWorkers++;
                $completedFiles++;
            }

            $drain();
        }

        while ($currentWorkers > 0) {
            $drain();
        }
    }
}
