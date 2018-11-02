<?php

namespace Jellyfish\Process;

interface ProcessInterface
{
    /**
     * @return void
     */
    public function start(): void;

    /**
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * @return array
     */
    public function getCommand(): array;
}
