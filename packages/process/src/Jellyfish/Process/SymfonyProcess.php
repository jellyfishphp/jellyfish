<?php

namespace Jellyfish\Process;

use Symfony\Component\Process\Process;

class SymfonyProcess implements ProcessInterface
{
    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * @param array $command
     */
    public function __construct(array $command)
    {
        $this->process = new Process($command);
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    /**
     * @return void
     */
    public function start(): void
    {
        $this->process->start();
    }
}
