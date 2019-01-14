<?php

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use DateTime;
use Jellyfish\Process\ProcessInterface;

class Job implements JobInterface
{
    /**
     * @var \Jellyfish\Process\ProcessInterface
     */
    protected $process;

    /**
     * @var \Cron\CronExpression
     */
    protected $cronExpression;

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
        if ($dateTime === null) {
            $dateTime = new DateTime();
        }

        if (!$this->isDue($dateTime)) {
            return $this;
        }

        $this->process->start();

        return $this;
    }
}
