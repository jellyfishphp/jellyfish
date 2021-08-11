<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;

class SchedulerFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\Scheduler\SchedulerFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $schedulerFactoryMock;

    /**
     * @var \Jellyfish\Scheduler\JobInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobMock;

    /**
     * @var \Jellyfish\Scheduler\SchedulerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $schedulerMock;
    /**
     * @var \Jellyfish\Scheduler\SchedulerFacade
     */
    protected SchedulerFacade $schedulerFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->schedulerFactoryMock = $this->getMockBuilder(SchedulerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->jobMock = $this->getMockBuilder(JobInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->schedulerMock = $this->getMockBuilder(SchedulerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->schedulerFacade = new SchedulerFacade($this->schedulerFactoryMock);
    }

    /**
     * @return void
     */
    public function testCreateJob(): void
    {
        $command = ['ls', '-la'];
        $cronExpression = '* * * * *';

        $this->schedulerFactoryMock->expects(static::atLeastOnce())
            ->method('createJob')
            ->with($command, $cronExpression)
            ->willReturn($this->jobMock);

        static::assertEquals($this->jobMock, $this->schedulerFacade->createJob($command, $cronExpression));
    }

    /**
     * @return void
     */
    public function testQueueJob(): void
    {
        $this->schedulerFactoryMock->expects(static::atLeastOnce())
            ->method('getScheduler')
            ->willReturn($this->schedulerMock);

        $this->schedulerMock->expects(static::atLeastOnce())
            ->method('queueJob')
            ->with($this->jobMock)
            ->willReturn($this->schedulerMock);

        static::assertEquals($this->schedulerFacade, $this->schedulerFacade->queueJob($this->jobMock));
    }

    /**
     * @return void
     */
    public function testRunScheduler(): void
    {
        $this->schedulerFactoryMock->expects(static::atLeastOnce())
            ->method('getScheduler')
            ->willReturn($this->schedulerMock);

        $this->schedulerMock->expects(static::atLeastOnce())
            ->method('run');

        static::assertEquals($this->schedulerFacade, $this->schedulerFacade->runScheduler());
    }

    /**
     * @return void
     */
    public function testGetQueuedJobs(): void
    {
        $queuedJobs = [$this->jobMock];

        $this->schedulerFactoryMock->expects(static::atLeastOnce())
            ->method('getScheduler')
            ->willReturn($this->schedulerMock);

        $this->schedulerMock->expects(static::atLeastOnce())
            ->method('getQueuedJobs')
            ->willReturn($queuedJobs);

        static::assertEquals($queuedJobs, $this->schedulerFacade->getQueuedJobs());
    }

    /**
     * @return void
     */
    public function testClearJobs(): void
    {
        $this->schedulerFactoryMock->expects(static::atLeastOnce())
            ->method('getScheduler')
            ->willReturn($this->schedulerMock);

        $this->schedulerMock->expects(static::atLeastOnce())
            ->method('clearJobs')
            ->willReturn($this->schedulerMock);

        static::assertEquals($this->schedulerFacade, $this->schedulerFacade->clearJobs());
    }
}
