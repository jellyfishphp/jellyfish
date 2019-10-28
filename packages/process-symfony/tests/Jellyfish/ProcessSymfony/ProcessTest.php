<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
use Jellyfish\Process\Exception\RuntimeException;

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

        $this->command = ['sleep', '5'];

        $this->symfonyProcess = new Process($this->command);
    }

    /**
     * @return void
     */
    public function testStart(): void
    {
        $this->assertInstanceOf(Process::class, $this->symfonyProcess->start());
    }

    /**
     * @return void
     */
    public function testStartStartedProcess(): void
    {
        $this->assertInstanceOf(Process::class, $this->symfonyProcess->start());
        $this->assertInstanceOf(Process::class, $this->symfonyProcess->start());
    }

    /**
     * @return void
     */
    public function testGetCommand(): void
    {
        $this->assertEquals($this->command, $this->symfonyProcess->getCommand());
    }
}
