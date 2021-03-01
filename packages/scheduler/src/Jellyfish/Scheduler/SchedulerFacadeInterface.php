<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

interface SchedulerFacadeInterface
{
    /**
     * @param string[] $command
     * @param string $cronExpression
     *
     * @return \Jellyfish\Scheduler\JobInterface
     */
    public function createJob(array $command, string $cronExpression): JobInterface;

    /**
     * @param \Jellyfish\Scheduler\JobInterface $job
     *
     * @return SchedulerFacadeInterface
     */
    public function queueJob(JobInterface $job): SchedulerFacadeInterface;

    /**
     * @return \Jellyfish\Scheduler\SchedulerFacadeInterface
     */
    public function runScheduler(): SchedulerFacadeInterface;

    /**
     * @return \Jellyfish\Scheduler\JobInterface[]
     */
    public function getQueuedJobs(): array;

    /**
     * @return \Jellyfish\Scheduler\SchedulerFacadeInterface
     */
    public function clearJobs(): SchedulerFacadeInterface;
}
