<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Process\ProcessInterface;

class SchedulerFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFacadeMock;
    /**
     * @var \Jellyfish\Process\ProcessInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processMock;

    /**
     * @var \Jellyfish\Scheduler\SchedulerFactory
     */
    protected $schedulerFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->processFacadeMock = $this->getMockBuilder(ProcessFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processMock = $this->getMockBuilder(ProcessInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->schedulerFactory = new SchedulerFactory($this->processFacadeMock);
    }

    /**
     * @return void
     */
    public function testCreateJob(): void
    {
        $command = ['ls', '-la'];
        $cronExpression = '* * * * *';

        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with($command)
            ->willReturn($this->processMock);

        static::assertInstanceOf(
            Job::class,
            $this->schedulerFactory->createJob($command, $cronExpression)
        );
    }

    /**
     * @return void
     */
    public function testGetScheduler(): void
    {
        static::assertInstanceOf(
            Scheduler::class,
            $this->schedulerFactory->getScheduler()
        );
    }
}
