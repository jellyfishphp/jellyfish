<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use DateTime;

class Scheduler implements SchedulerInterface
{
    protected const DELAY_INTERVAL = 60_000_000;

    /**
     * @var JobInterface[]
     */
    protected array $jobs;

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
     * @return void
     *
     * @throws \Exception
     *
     * @codeCoverageIgnore
     */
    public function run(): void
    {
        while (true) {
            $dateTime = new DateTime();

            foreach ($this->jobs as $job) {
                if ($job->isRunning()) {
                    continue;
                }

                $job->run($dateTime);
            }

            usleep(static::DELAY_INTERVAL);
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
