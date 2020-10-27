<?php

declare(strict_types=1);

namespace Jellyfish\Process;

interface ProcessInterface
{
    /**
     * @return \Jellyfish\Process\ProcessInterface
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
}
