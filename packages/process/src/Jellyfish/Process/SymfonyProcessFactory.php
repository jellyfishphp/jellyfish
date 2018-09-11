<?php

namespace Jellyfish\Process;

class SymfonyProcessFactory implements ProcessFactoryInterface
{
    /**
     * @param array $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function create(array $command): ProcessInterface
    {
        return new SymfonyProcess($command);
    }
}
