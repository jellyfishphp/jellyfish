<?php

declare(strict_types=1);

namespace Jellyfish\Process;

interface ProcessFacadeInterface
{
    /**
     * @param string[] $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function createProcess(array $command): ProcessInterface;
}
