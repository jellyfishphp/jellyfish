<?php

declare(strict_types = 1);

namespace Jellyfish\Scheduler\Command;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockInterface;
use Jellyfish\Scheduler\SchedulerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunSchedulerCommandTest extends Unit
{
    protected MockObject&InputInterface $inputMock;

    protected MockObject&OutputInterface $outputMock;

    protected SchedulerInterface&MockObject $schedulerMock;

    protected MockObject&LockFactoryInterface $lockFactoryMock;

    protected LockInterface&MockObject $lockMock;

    protected MockObject&LoggerInterface $loggerMock;

    /**
     * @var array<string>
     */
    protected array $lockIdentifierParts;

    protected RunSchedulerCommand $runSchedulerCommand;

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

        $this->schedulerMock = $this->getMockBuilder(SchedulerInterface::class)
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
            $this->schedulerMock,
            $this->lockFactoryMock,
            $this->loggerMock,
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertSame(RunSchedulerCommand::NAME, $this->runSchedulerCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertSame(RunSchedulerCommand::DESCRIPTION, $this->runSchedulerCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->schedulerMock->expects($this->atLeastOnce())
            ->method('run');

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $this->loggerMock->expects($this->never())
            ->method('error');

        $exitCode = $this->runSchedulerCommand->run($this->inputMock, $this->outputMock);

        $this->assertSame(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithLockedStatus(): void
    {
        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(false);

        $this->schedulerMock->expects($this->never())
            ->method('run');

        $this->lockMock->expects($this->never())
            ->method('release')
            ->willReturn($this->lockMock);

        $this->loggerMock->expects($this->never())
            ->method('error');

        $exitCode = $this->runSchedulerCommand->run($this->inputMock, $this->outputMock);

        $this->assertSame(0, $exitCode);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithException(): void
    {
        $exceptionMessage = 'Test exception';

        $this->lockFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($this->lockIdentifierParts, 360.0)
            ->willReturn($this->lockMock);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('acquire')
            ->willReturn(true);

        $this->schedulerMock->expects($this->atLeastOnce())
            ->method('run')
            ->willThrowException(new Exception($exceptionMessage));

        $this->loggerMock->expects($this->atLeastOnce())
            ->method('error')
            ->with($exceptionMessage);

        $this->lockMock->expects($this->atLeastOnce())
            ->method('release')
            ->willReturn($this->lockMock);

        $exitCode = $this->runSchedulerCommand->run($this->inputMock, $this->outputMock);

        $this->assertSame(0, $exitCode);
    }
}
