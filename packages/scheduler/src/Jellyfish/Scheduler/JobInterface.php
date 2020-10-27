<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use DateTime;

interface JobInterface
{
    /**
     * @return array
     */
    public function getCommand(): array;

    /**
     * @return \Cron\CronExpression
     */
    public function getCronExpression(): CronExpression;

    /**
     * @param \DateTime|null $dateTime
     *
     * @return \Jellyfish\Scheduler\JobInterface
     */
    public function run(?DateTime $dateTime = null): JobInterface;

    /**
     * @return bool
     */
    public function isRunning(): bool;
}
