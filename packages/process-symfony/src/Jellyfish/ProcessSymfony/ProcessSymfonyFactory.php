<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\ProcessInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

class ProcessSymfonyFactory
{
    /**
     * @param array $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function createProcess(array $command): ProcessInterface
    {
        return new Process(
            $command,
            $this->createSymfonyProcess($command)
        );
    }

    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function createSymfonyProcess(array $command): SymfonyProcess
    {
        return new SymfonyProcess($command);
    }
}
