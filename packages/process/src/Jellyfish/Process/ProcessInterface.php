<?php

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
}
