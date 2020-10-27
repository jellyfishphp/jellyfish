<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\ProcessInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process implements ProcessInterface
{
    /**
     * @var array
     */
    protected $command;

    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * @param array $command
     */
    public function __construct(array $command)
    {
        $this->command = $command;
        $this->process = new SymfonyProcess($command);
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
     * @return array
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
