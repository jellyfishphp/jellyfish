<?php

namespace Jellyfish\Process;

use Jellyfish\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class SymfonyProcess implements ProcessInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $command;

    /**
     * @var string
     */
    protected $pathToLockFile;

    /**
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * @param array $command
     * @param string $tempDir
     */
    public function __construct(array $command, string $tempDir)
    {
        $this->id = \sha1(implode(' ', $command));
        $this->command = $command;
        $this->pathToLockFile = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->id;

        $preparedCommand = array_merge($command, [';', 'rm', $this->pathToLockFile]);

        $this->process = new Process($preparedCommand);
    }

    /**
     * @return void
     */
    public function start(): void
    {
        if ($this->isLocked()) {
            throw new RuntimeException('Process is locked.');
        }

        $this->lock();
        $this->process->start();
    }

    /**
     * @return void
     */
    protected function lock(): void
    {
        touch($this->pathToLockFile);
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return file_exists($this->pathToLockFile);
    }

    /**
     * @return array
     */
    public function getCommand(): array
    {
        return $this->command;
    }
}
