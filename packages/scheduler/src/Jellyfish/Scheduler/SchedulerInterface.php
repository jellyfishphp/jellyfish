<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

interface SchedulerInterface
{
    /**
     * @param \Jellyfish\Scheduler\JobInterface $job
     *
     * @return \Jellyfish\Scheduler\SchedulerInterface
     */
    public function queueJob(JobInterface $job): SchedulerInterface;

    /**
     * @return \Jellyfish\Scheduler\JobInterface[]
     */
    public function getQueuedJobs(): array;

    /**
     * @return \Jellyfish\Scheduler\SchedulerInterface
     */
    public function clearJobs(): SchedulerInterface;

    /**
     * @return \Jellyfish\Scheduler\SchedulerInterface
     */
    public function run(): SchedulerInterface;
}
