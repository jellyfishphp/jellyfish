<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
use Jellyfish\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process as SymfonyProcess;

class ProcessTest extends Unit
{
    /**
     * @var string[]
     */
    protected $command;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Process\Process
     */
    protected $symfonyProcessMock;

    /**
     * @var \Jellyfish\Process\ProcessInterface
     */
    protected $process;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->command = ['sleep', '5'];

        $this->symfonyProcessMock = $this->getMockBuilder(SymfonyProcess::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->process = new Process($this->command, $this->symfonyProcessMock);
    }

    /**
     * @return void
     */
    public function testStart(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isRunning')
            ->willReturn(false);

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('start');

        static::assertInstanceOf(Process::class, $this->process->start());
    }

    /**
     * @return void
     */
    public function testStartStartedProcess(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isRunning')
            ->willReturn(true);

        $this->symfonyProcessMock->expects(static::never())
            ->method('start');

        static::assertInstanceOf(Process::class, $this->process->start());
    }

    /**
     * @return void
     */
    public function testGetCommand(): void
    {
        static::assertEquals($this->command, $this->process->getCommand());
    }

    /**
     * @return void
     */
    public function testIsRunning(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isRunning')
            ->willReturn(true);

        static::assertTrue($this->process->isRunning());
    }
}
