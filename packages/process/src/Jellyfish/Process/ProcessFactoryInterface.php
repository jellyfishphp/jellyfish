<?php

declare(strict_types=1);

namespace Jellyfish\Process;

interface ProcessFactoryInterface
{
    /**
     * @param array $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function create(array $command): ProcessInterface;
}
