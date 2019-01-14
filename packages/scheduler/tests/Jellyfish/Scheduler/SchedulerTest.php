<?php

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;

class SchedulerTest extends Unit
{
    /**
     * @var \Jellyfish\Scheduler\SchedulerInterface
     */
    protected $scheduler;

    /**
     * @var \Jellyfish\Scheduler\JobInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $jobMock;

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
     * @depends testQueueJobAndGetQueuedJobs
     *
     * @return void
     */
    public function testQueueJobAndRun(): void
    {
        $this->assertEquals($this->scheduler, $this->scheduler->queueJob($this->jobMock));

        $this->jobMock->expects($this->atLeastOnce())
            ->method('run')
            ->withAnyParameters(); // TODO: only DateTime

        $this->assertEquals($this->scheduler, $this->scheduler->run());
    }

    /**
     * @depends testQueueJobAndGetQueuedJobs
     *
     * @return void
     */
    public function testGetQueuedJobsAfterQueueJobAndClearJobs(): void
    {
        $this->assertEquals($this->scheduler, $this->scheduler->queueJob($this->jobMock));
        $this->assertEquals($this->scheduler, $this->scheduler->clearJobs());

        $queuedJobs = $this->scheduler->getQueuedJobs();
        $this->assertCount(0, $queuedJobs);
    }
}
