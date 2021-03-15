<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
use Exception;
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

        try {
            $this->process->start();
            static::fail();
        } catch (Exception $exception) {
        }
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

    /**
     * @return void
     */
    public function testWait(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isStarted')
            ->willReturn(true);

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('wait');

        static::assertInstanceOf(Process::class, $this->process->wait());
    }

    /**
     * @return void
     */
    public function testGetTimeout(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('getTimeout')
            ->willReturn(1.0);

        static::assertEquals(1, $this->process->getTimeout());
    }

    /**
     * @return void
     */
    public function testSetTimeout(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('setTimeout')
            ->with(1.0);

        static::assertInstanceOf(Process::class, $this->process->setTimeout(1));
    }

    /**
     * @return void
     */
    public function testGetOutputForError(): void
    {
        $errorOutput = 'Error output';

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isStarted')
            ->willReturn(true);

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isSuccessful')
            ->willReturn(false);

        $this->symfonyProcessMock->expects(static::never())
            ->method('getOutput');

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('getErrorOutput')
            ->willReturn($errorOutput);

        static::assertEquals($errorOutput, $this->process->getOutput());
    }

    /**
     * @return void
     */
    public function testGetOutput(): void
    {
        $output = 'Output';

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isStarted')
            ->willReturn(true);

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('getOutput')
            ->willReturn($output);

        $this->symfonyProcessMock->expects(static::never())
            ->method('getErrorOutput');

        static::assertEquals($output, $this->process->getOutput());
    }

    /**
     * @return void
     */
    public function testGetOutputWithoutStartedStatus(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isStarted')
            ->willReturn(false);

        $this->symfonyProcessMock->expects(static::never())
            ->method('isSuccessful')
            ->willReturn(true);

        $this->symfonyProcessMock->expects(static::never())
            ->method('getOutput');

        $this->symfonyProcessMock->expects(static::never())
            ->method('getErrorOutput');

        try {
            $this->process->getOutput();
            static::fail();
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    public function testGetExitCode(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isTerminated')
            ->willReturn(true);

        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('getExitCode')
            ->willReturn(0);

        static::assertEquals(0, $this->process->getExitCode());
    }

    /**
     * @return void
     */
    public function testGetExitCodeWithoutTerminatedStatus(): void
    {
        $this->symfonyProcessMock->expects(static::atLeastOnce())
            ->method('isTerminated')
            ->willReturn(false);

        $this->symfonyProcessMock->expects(static::never())
            ->method('getExitCode');

        try {
            $this->process->getExitCode();
            static::fail();
        } catch (Exception $exception) {
        }
    }
}
