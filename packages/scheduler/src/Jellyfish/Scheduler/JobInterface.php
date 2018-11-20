<?php

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
     * @return void
     */
    public function run(?DateTime $dateTime = null): void;
}
