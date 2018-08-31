<?php

namespace Jellyfish\Scheduler;

interface SchedulerInterface
{
    /**
     * @param \Jellyfish\Scheduler\JobInterface $job
     *
     * @return void
     */
    public function queueJob(JobInterface $job): void;

    /**
     * @return \Jellyfish\Scheduler\JobInterface[]
     */
    public function getQueuedJobs(): array;

    /**
     * @return void
     */
    public function clearJobs(): void;

    /**
     * @return void
     */
    public function run(): void;
}
