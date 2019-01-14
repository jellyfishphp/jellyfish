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
     * @return \Jellyfish\Scheduler\SchedulerInterface
     */
    public function queueJob(JobInterface $job): SchedulerInterface
    {
        $this->jobs[] = $job;

        return $this;
    }

    /**
     * @return \Jellyfish\Scheduler\SchedulerInterface
     */
    public function clearJobs(): SchedulerInterface
    {
        $this->jobs = [];

        return $this;
    }

    /**
     * @return \Jellyfish\Scheduler\SchedulerInterface
     *
     * @throws \Exception
     */
    public function run(): SchedulerInterface
    {
        $dateTime = new DateTime();

        foreach ($this->jobs as $job) {
            $job->run($dateTime);
        }

        return $this;
    }

    /**
     * @return \Jellyfish\Scheduler\JobInterface[]
     */
    public function getQueuedJobs(): array
    {
        return $this->jobs;
    }
}
