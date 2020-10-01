<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Cron\CronExpression;
use DateTime;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Process\ProcessInterface;

class JobFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Scheduler\CronExpressionFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $cronExpressionFactoryMock;

    /**
     * @var \Jellyfish\Process\ProcessFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFactoryMock;

    /**
     * @var \Jellyfish\Scheduler\JobFactoryInterface
     */
    protected $jobFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cronExpressionFactoryMock = $this->getMockBuilder(CronExpressionFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processFactoryMock = $this->getMockBuilder(ProcessFactoryInterface::class)->disableOriginalConstructor()->getMock();

        $this->jobFactory = new JobFactory($this->processFactoryMock, $this->cronExpressionFactoryMock);
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $job = $this->jobFactory->create([], '* * * * *');
        $this->assertInstanceOf(Job::class, $job);
    }
}
