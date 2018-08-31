<?php

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use DateTime;

interface JobInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getCommand(): string;

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
