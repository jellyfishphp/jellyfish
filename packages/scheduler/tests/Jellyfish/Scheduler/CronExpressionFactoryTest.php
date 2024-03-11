<?php

declare(strict_types = 1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Cron\CronExpression;

class CronExpressionFactoryTest extends Unit
{
    protected CronExpressionFactory $cronExpressionFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cronExpressionFactory = new CronExpressionFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $cronExpression = $this->cronExpressionFactory->create('* * * * *');
        $this->assertInstanceOf(CronExpression::class, $cronExpression);
    }
}
