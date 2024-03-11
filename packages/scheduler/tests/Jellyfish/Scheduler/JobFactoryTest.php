<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

class JobFactoryTest extends Unit
{
    protected CronExpressionFactoryInterface&MockObject $cronExpressionFactoryMock;

    protected MockObject&ProcessFactoryInterface $processFactoryMock;

    protected JobFactory $jobFactory;

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
