<?php

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
use Jellyfish\Process\Exception\RuntimeException;
use org\bovigo\vfs\vfsStream;

class ProcessTest extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessInterface
     */
    protected $symfonyProcess;

    /**
     * @var array
     */
    protected $command;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $tempDir = vfsStream::setup('tmp')->url();
        $tempDir = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->command = ['sleep', '5'];

        $this->symfonyProcess = new Process($this->command, $tempDir);
    }

    /**
     * @return void
     */
    public function testStartAndIsLocked(): void
    {
        $this->symfonyProcess->start();
        $this->assertTrue($this->symfonyProcess->isLocked());
    }

    /**
     * @return void
     */
    public function testStartLocked(): void
    {
        $this->symfonyProcess->start();

        try {
            $this->symfonyProcess->start();
            $this->fail();
        } catch (RuntimeException $e) {
        }
    }

    /**
     * @return void
     */
    public function testGetCommand(): void
    {
        $this->assertEquals($this->command, $this->symfonyProcess->getCommand());
    }
}
