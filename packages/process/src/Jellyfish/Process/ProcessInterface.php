<?php

namespace Jellyfish\Process;

interface ProcessInterface
{
    /**
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * @return void
     */
    public function start(): void;
}
