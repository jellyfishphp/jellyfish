<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use DateTime;

use function count;

class Scheduler implements SchedulerInterface
{
    protected const DELAY_INTERVAL = 1000000;

    /**
     * @var JobInterface[]
     */
    protected $jobs;

    /**
     * @var JobInterface[]
     */
    protected $runningJobs = [];

    /**
     * Scheduler constructor
     */
    public function __construct()
    {
        $this->jobs = [];
        $this->runningJobs = [];
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
            $this->runningJobs[] = $job->run($dateTime);
        }

        while (count($this->runningJobs) !== 0) {
            foreach ($this->runningJobs as $index => $runningJob) {
                if ($runningJob->isRunning()) {
                    continue;
                }

                unset($this->runningJobs[$index]);
            }

            usleep(static::DELAY_INTERVAL);
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
