<?php

namespace Jellyfish\Scheduler;

use Cron\CronExpression;

class CronExpressionFactory implements CronExpressionFactoryInterface
{
    /**
     * @param  string  $expression
     *
     * @return \Cron\CronExpression
     */
    public function create(string $expression): CronExpression
    {
        return CronExpression::factory($expression);
    }
}
