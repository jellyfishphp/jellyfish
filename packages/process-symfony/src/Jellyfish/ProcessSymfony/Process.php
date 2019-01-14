<?php

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
        $preparedCommand = \implode(' ', $this->command);

        $this->process = new SymfonyProcess($preparedCommand);
    }

    /**
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function start(): ProcessInterface
    {
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
}
