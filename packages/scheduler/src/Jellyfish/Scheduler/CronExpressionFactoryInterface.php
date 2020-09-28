<?php

namespace Jellyfish\Scheduler;

use Cron\CronExpression;

interface CronExpressionFactoryInterface
{
    /**
     * @param  string  $expression
     *
     * @return \Cron\CronExpression
     */
    public function create(string $expression): CronExpression;
}
