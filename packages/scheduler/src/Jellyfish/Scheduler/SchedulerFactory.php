<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use Jellyfish\Process\ProcessFacadeInterface;

class SchedulerFactory
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected ProcessFacadeInterface $processFacade;

    /**
     * @var \Jellyfish\Scheduler\SchedulerInterface|null
     */
    protected ?SchedulerInterface $scheduler = null;

    /**
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     */
    public function __construct(ProcessFacadeInterface $processFacade)
    {
        $this->processFacade = $processFacade;
    }

    /**
     * @param string $expression
     *
     * @return \Cron\CronExpression
     */
    protected function createCronExpression(string $expression): CronExpression
    {
        return CronExpression::factory($expression);
    }

    /**
     * @param string[] $command
     * @param string $cronExpression
     *
     * @return \Jellyfish\Scheduler\JobInterface
     */
    public function createJob(array $command, string $cronExpression): JobInterface
    {
        return new Job(
            $this->processFacade->createProcess($command),
            $this->createCronExpression($cronExpression)
        );
    }

    /**
     * @return \Jellyfish\Scheduler\SchedulerInterface
     */
    public function getScheduler(): SchedulerInterface
    {
        if ($this->scheduler === null) {
            $this->scheduler = new Scheduler();
        }

        return $this->scheduler;
    }
}
