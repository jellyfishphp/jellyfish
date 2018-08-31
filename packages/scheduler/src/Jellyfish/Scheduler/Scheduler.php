<?php

namespace Jellyfish\Scheduler;

use DateTime;

class Scheduler implements SchedulerInterface
{
    /**
     * @var JobInterface[]
     */
    protected $jobs;

    /**
     * Scheduler constructor
     */
    public function __construct()
    {
        $this->jobs = [];
    }

    /**
     * @param \Jellyfish\Scheduler\JobInterface $job
     *
     * @return void
     */
    public function queueJob(JobInterface $job): void
    {
        $this->jobs[] = $job;
    }

    /**
     * @return void
     */
    public function clearJobs(): void
    {
        $this->jobs = [];
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $dateTime = new DateTime();

        foreach ($this->jobs as $job) {
            $job->run($dateTime);
        }
    }

    /**
     * @return \Jellyfish\Scheduler\JobInterface[]
     */
    public function getQueuedJobs(): array
    {
        return $this->jobs;
    }
}
