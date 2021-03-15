<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\Exception\AlreadyStartedException;
use Jellyfish\Process\Exception\NotStartedException;
use Jellyfish\Process\Exception\NotTerminatedException;
use Jellyfish\Process\ProcessInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

use function sprintf;

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
            throw new AlreadyStartedException('Process is already started.');
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

    /**
     * @return \Jellyfish\Process\ProcessInterface
     *
     * @throws \Jellyfish\Process\Exception\NotStartedException
     */
    public function wait(): ProcessInterface
    {
        $this->requireIsStarted(__METHOD__);

        $this->process->wait();

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimeout(): ?int
    {
        return $this->process->getTimeout() === null ? null : (int)$this->process->getTimeout();
    }

    /**
     * @param int|null $timeout
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function setTimeout(?int $timeout): ProcessInterface
    {
        $this->process->setTimeout($timeout === null ? null : (float)$timeout);

        return $this;
    }

    /**
     * @return string
     *
     * @throws \Jellyfish\Process\Exception\NotStartedException
     */
    public function getOutput(): string
    {
        $this->requireIsStarted(__METHOD__);

        if ($this->process->isSuccessful()) {
            return $this->process->getOutput();
        }

        return $this->process->getErrorOutput();
    }

    /**
     * @return int
     *
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
     */
    public function getExitCode(): int
    {
        $this->requireIsTerminated(__METHOD__);

        return $this->process->getExitCode() ?? 1;
    }

    /**
     * @param string $methodName
     *
     * @return \Jellyfish\Process\ProcessInterface
     *
     * @throws \Jellyfish\Process\Exception\NotStartedException
     */
    protected function requireIsStarted(string $methodName): ProcessInterface
    {
        if (!$this->process->isStarted()) {
            throw new NotStartedException(sprintf('Process must be started before calling %s()', $methodName));
        }

        return $this;
    }

    /**
     * @param string $methodName
     *
     * @return \Jellyfish\Process\ProcessInterface
     *
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
     */
    protected function requireIsTerminated(string $methodName): ProcessInterface
    {
        if (!$this->process->isTerminated()) {
            throw new NotTerminatedException(sprintf('Process must be terminated before calling %s()', $methodName));
        }

        return $this;
    }
}
