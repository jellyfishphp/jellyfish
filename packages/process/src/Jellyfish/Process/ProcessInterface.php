<?php

declare(strict_types=1);

namespace Jellyfish\Process;

interface ProcessInterface
{
    /**
     * @return \Jellyfish\Process\ProcessInterface
     *
     * @throws \Jellyfish\Process\Exception\AlreadyStartedException
     */
    public function start(): ProcessInterface;

    /**
     * @return array
     */
    public function getCommand(): array;

    /**
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * @return \Jellyfish\Process\ProcessInterface
     *
     * @throws \Jellyfish\Process\Exception\NotStartedException
     */
    public function wait(): ProcessInterface;

    /**
     * @return int|null
     */
    public function getTimeout(): ?int;

    /**
     * @param int|null $timeout
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function setTimeout(?int $timeout): ProcessInterface;

    /**
     * @return string
     *
     * @throws \Jellyfish\Process\Exception\NotStartedException
     */
    public function getOutput(): string;

    /**
     * @return int
     *
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
     */
    public function getExitCode(): int;
}
