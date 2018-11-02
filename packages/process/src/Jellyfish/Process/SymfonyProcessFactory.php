<?php

namespace Jellyfish\Process;

class SymfonyProcessFactory implements ProcessFactoryInterface
{
    protected $tempDir;

    /**
     * @param string $tempDir
     */
    public function __construct(string $tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @param array $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function create(array $command): ProcessInterface
    {
        return new SymfonyProcess($command, $this->tempDir);
    }
}
