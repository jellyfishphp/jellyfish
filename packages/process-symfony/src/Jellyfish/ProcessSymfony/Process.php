<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\ProcessInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process implements ProcessInterface
{
    /**
     * @var string[]
     */
    protected $command;

    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * @param string[] $command
     * @param \Symfony\Component\Process\Process $process
     */
    public function __construct(array $command, SymfonyProcess $process)
    {
        $this->command = $command;
        $this->process = $process;
    }

    /**
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function start(): ProcessInterface
    {
        if ($this->isRunning()) {
            return $this;
        }

        $this->process->start();

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCommand(): array
    {
        return $this->command;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }
}
