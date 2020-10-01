<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Cron\CronExpression;
use DateTime;
use Jellyfish\Process\ProcessInterface;

interface JobFactoryInterface
{
    /**
     * @param  array  $command
     * @param  string  $cronExpression
     *
     * @return \Jellyfish\Scheduler\JobInterface
     */
    public function create(array $command, string $cronExpression): JobInterface;
}
