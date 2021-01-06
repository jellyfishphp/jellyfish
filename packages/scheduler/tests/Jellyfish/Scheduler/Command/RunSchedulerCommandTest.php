<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler\Command;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockInterface;
use Jellyfish\Scheduler\SchedulerFacadeInterface;
use Jellyfish\Scheduler\SchedulerInterface;
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
     * @var \Jellyfish\Lock\LockFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockFactoryMock;

    /**
     * @var \Jellyfish\Lock\LockInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $lockMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

    /**
     * @var \Jellyfish\Scheduler\Command\RunSchedulerCommand
     */
    protected $runSchedulerCommand;

    /**
     * @var array
     */
    protected $lockIdentifierParts;

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

        $this->lockFactoryMock = $this->getMockBuilder(LockFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockMock = $this->getMockBuilder(LockInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->lockIdentifierParts = [RunSchedulerCommand::NAME];

        $this->runSchedulerCommand = new RunSchedulerCommand(
            $this->schedulerFacadeMock,
            $this->lockFactoryMock,
            $this->loggerMock
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
        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
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

        $this->loggerMock->expects(static::never())
            ->method('error');

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
        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
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

        $this->loggerMock->expects(static::never())
            ->method('error');

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

        $this->lockFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects(static::atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->schedulerFacadeMock->expects(static::atLeastOnce())
            ->method('runScheduler')
            ->willThrowException(new Exception($exceptionMessage));

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
