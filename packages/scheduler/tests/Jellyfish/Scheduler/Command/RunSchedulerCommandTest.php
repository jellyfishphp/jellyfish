<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler\Command;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Lock\LockFacadeInterface;
use Jellyfish\Lock\LockInterface;
use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\Scheduler\SchedulerFacadeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunSchedulerCommandTest extends Unit
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inputMock;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $outputMock;

    /**
     * @var \Jellyfish\Scheduler\SchedulerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $schedulerFacadeMock;

    /**
     * @var \Jellyfish\Lock\LockFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockFacadeMock;

    /**
     * @var \Jellyfish\Lock\LockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockMock;

    /**
     * @var \Jellyfish\Log\LogFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $logFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var \Jellyfish\Scheduler\Command\RunSchedulerCommand
     */
    protected RunSchedulerCommand $runSchedulerCommand;

    /**
     * @var array
     */
    protected array $lockIdentifierParts;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->inputMock = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->outputMock = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->schedulerFacadeMock = $this->getMockBuilder(SchedulerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockFacadeMock = $this->getMockBuilder(LockFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockMock = $this->getMockBuilder(LockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logFacadeMock = $this->getMockBuilder(LogFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockIdentifierParts = [RunSchedulerCommand::NAME];

        $this->runSchedulerCommand = new RunSchedulerCommand(
            $this->schedulerFacadeMock,
            $this->lockFacadeMock,
            $this->logFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        static::assertEquals(RunSchedulerCommand::NAME, $this->runSchedulerCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        static::assertEquals(RunSchedulerCommand::DESCRIPTION, $this->runSchedulerCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->schedulerFacadeMock->expects(static::atLeastOnce())
            ->method('runScheduler');

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $this->logFacadeMock->expects(static::never())
            ->method('getLogger');

        $exitCode = $this->runSchedulerCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithLockedStatus(): void
    {
        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(false);

        $this->schedulerFacadeMock->expects(static::never())
            ->method('runScheduler');

        $this->lockMock->expects(static::never())
            ->method('release')
            ->willReturn($this->lockMock);

        $this->logFacadeMock->expects(static::never())
            ->method('getLogger');

        $exitCode = $this->runSchedulerCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithException(): void
    {
        $exceptionMessage = 'Test exception';

        $this->lockFacadeMock->expects(static::atLeastOnce())
            ->method('createLock')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->schedulerFacadeMock->expects(static::atLeastOnce())
            ->method('runScheduler')
            ->willThrowException(new Exception($exceptionMessage));

        $this->logFacadeMock->expects(static::atLeastOnce())
            ->method('getLogger')
            ->willReturn($this->loggerMock);

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('error')
            ->with($exceptionMessage);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->runSchedulerCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }
}
