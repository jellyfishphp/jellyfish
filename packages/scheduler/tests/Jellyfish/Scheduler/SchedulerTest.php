<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;

class SchedulerTest extends Unit
{
    /**
     * @var \Jellyfish\Scheduler\SchedulerInterface
     */
    protected SchedulerInterface $scheduler;

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
        static::assertEquals($this->scheduler, $this->scheduler->queueJob($this->jobMock));

        $queuedJobs = $this->scheduler->getQueuedJobs();
        static::assertCount(1, $queuedJobs);
        static::assertEquals($this->jobMock, $queuedJobs[0]);
    }

    /**
     * @depends testQueueJobAndGetQueuedJobs
     *
     * @return void
     */
    public function testGetQueuedJobsAfterQueueJobAndClearJobs(): void
    {
        static::assertEquals($this->scheduler, $this->scheduler->queueJob($this->jobMock));
        static::assertEquals($this->scheduler, $this->scheduler->clearJobs());

        $queuedJobs = $this->scheduler->getQueuedJobs();
        static::assertCount(0, $queuedJobs);
    }
}
