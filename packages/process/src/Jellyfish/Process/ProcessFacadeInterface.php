<?php

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
