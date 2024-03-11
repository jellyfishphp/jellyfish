<?php

declare(strict_types = 1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\MockObject\MockObject;

class SchedulerTest extends Unit
{
    protected MockObject&JobInterface $jobMock;

    protected Scheduler $scheduler;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->jobMock = $this->getMockBuilder(JobInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->scheduler = new Scheduler();
    }

    /**
     * @return void
     */
    public function testQueueJobAndGetQueuedJobs(): void
    {
        $this->assertEquals($this->scheduler, $this->scheduler->queueJob($this->jobMock));

        $queuedJobs = $this->scheduler->getQueuedJobs();
        $this->assertCount(1, $queuedJobs);
        $this->assertEquals($this->jobMock, $queuedJobs[0]);
    }

    /**
     * @return void
     */
    #[Depends('testQueueJobAndGetQueuedJobs')]
    public function testQueueJobAndRun(): void
    {
        $this->assertEquals($this->scheduler, $this->scheduler->queueJob($this->jobMock));

        $this->jobMock->expects($this->atLeastOnce())
            ->method('run')
            ->withAnyParameters()
            ->willReturn($this->jobMock); // TODO: only DateTime

        $this->jobMock->expects($this->exactly(2))
            ->method('isRunning')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->assertEquals($this->scheduler, $this->scheduler->run());
    }

    /**
     * @return void
     */
    #[Depends('testQueueJobAndGetQueuedJobs')]
    public function testGetQueuedJobsAfterQueueJobAndClearJobs(): void
    {
        $this->assertEquals($this->scheduler, $this->scheduler->queueJob($this->jobMock));
        $this->assertEquals($this->scheduler, $this->scheduler->clearJobs());

        $queuedJobs = $this->scheduler->getQueuedJobs();
        $this->assertCount(0, $queuedJobs);
    }
}
