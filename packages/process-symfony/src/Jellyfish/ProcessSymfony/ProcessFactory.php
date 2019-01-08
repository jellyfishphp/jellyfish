<?php

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Process\ProcessInterface;

class ProcessFactory implements ProcessFactoryInterface
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
        return new Process($command, $this->tempDir);
    }
}
