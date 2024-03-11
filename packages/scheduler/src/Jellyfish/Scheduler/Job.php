<?php

declare(strict_types = 1);

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use DateTime;
use Jellyfish\Process\ProcessInterface;

/**
 * @see \Jellyfish\Scheduler\JobTest
 */
class Job implements JobInterface
{
    protected ProcessInterface $process;

    protected CronExpression $cronExpression;

    /**
     * @param \Jellyfish\Process\ProcessInterface $process
     * @param \Cron\CronExpression $cronExpression
     */
    public function __construct(
        ProcessInterface $process,
        CronExpression $cronExpression
    ) {
        $this->process = $process;
        $this->cronExpression = $cronExpression;
    }

    /**
     * @return array
     */
    public function getCommand(): array
    {
        return $this->process->getCommand();
    }

    /**
     * @return \Cron\CronExpression
     */
    public function getCronExpression(): CronExpression
    {
        return $this->cronExpression;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    protected function isDue(DateTime $dateTime): bool
    {
        return $this->cronExpression->isDue($dateTime);
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return \Jellyfish\Scheduler\JobInterface
     *
     * @throws \Exception
     */
    public function run(?DateTime $dateTime = null): JobInterface
    {
        if (!$dateTime instanceof DateTime) {
            $dateTime = new DateTime();
        }

        if (!$this->isDue($dateTime)) {
            return $this;
        }

        $this->process->start();

        return $this;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }
}
