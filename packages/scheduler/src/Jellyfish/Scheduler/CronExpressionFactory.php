<?php

declare(strict_types = 1);

namespace Jellyfish\Scheduler;

use Cron\CronExpression;

/**
 * @see \Jellyfish\Scheduler\CronExpressionFactoryTest
 */
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
