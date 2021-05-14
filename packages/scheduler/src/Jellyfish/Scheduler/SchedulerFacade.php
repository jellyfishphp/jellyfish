<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

class SchedulerFacade implements SchedulerFacadeInterface
{
    /**
     * @var \Jellyfish\Scheduler\SchedulerFactory
     */
    protected SchedulerFactory $factory;

    /**
     * @param \Jellyfish\Scheduler\SchedulerFactory $factory
     */
    public function __construct(SchedulerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string[] $command
     * @param string $cronExpression
     *
     * @return \Jellyfish\Scheduler\JobInterface
     */
    public function createJob(array $command, string $cronExpression): JobInterface
    {
        return $this->factory->createJob($command, $cronExpression);
    }

    /**
     * @param \Jellyfish\Scheduler\JobInterface $job
     *
     * @return SchedulerFacadeInterface
     */
    public function queueJob(JobInterface $job): SchedulerFacadeInterface
    {
        $this->factory->getScheduler()->queueJob($job);

        return $this;
    }

    /**
     * @return \Jellyfish\Scheduler\SchedulerFacadeInterface
     */
    public function runScheduler(): SchedulerFacadeInterface
    {
        $this->factory->getScheduler()->run();

        return $this;
    }

    /**
     * @return \Jellyfish\Scheduler\JobInterface[]
     */
    public function getQueuedJobs(): array
    {
        return $this->factory->getScheduler()->getQueuedJobs();
    }

    /**
     * @return \Jellyfish\Scheduler\SchedulerFacadeInterface
     */
    public function clearJobs(): SchedulerFacadeInterface
    {
        $this->factory->getScheduler()->clearJobs();

        return $this;
    }
}
