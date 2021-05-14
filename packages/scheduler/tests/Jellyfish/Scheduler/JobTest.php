<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Cron\CronExpression;
use DateTime;
use Jellyfish\Process\ProcessInterface;

class JobTest extends Unit
{
    /**
     * @var \Jellyfish\Scheduler\JobInterface
     */
    protected JobInterface $job;

    /**
     * @var \Cron\CronExpression|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $cronExpressionMock;

    /**
     * @var \Jellyfish\Process\ProcessInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processMock;

    /**
     * @var DateTime
     */
    protected DateTime $dateTime;

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        parent::_before();

        $this->dateTime = new DateTime();

        $this->cronExpressionMock = $this->getMockBuilder(CronExpression::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processMock = $this->getMockForAbstractClass(ProcessInterface::class);

        $this->job = new Job($this->processMock, $this->cronExpressionMock);
    }

    /**
     * @return void
     */
    public function testGetCommand(): void
    {
        $expectedCommand = ['ls', '-la'];

        $this->processMock->expects(static::atLeastOnce())
            ->method('getCommand')
            ->willReturn($expectedCommand);

        static::assertEquals($expectedCommand, $this->job->getCommand());
    }

    /**
     * @return void
     */
    public function testGetCronExpression(): void
    {
        static::assertEquals($this->cronExpressionMock, $this->job->getCronExpression());
    }

    /**
     * @return void
     */
    public function testRun(): void
    {
        $this->cronExpressionMock->expects(static::atLeastOnce())
            ->method('isDue')
            ->with($this->dateTime)
            ->willReturn(true);

        static::assertEquals($this->job, $this->job->run($this->dateTime));
    }

    /**
     * @return void
     */
    public function testRunWithoutDueDateTime(): void
    {
        $this->cronExpressionMock->expects(static::atLeastOnce())
            ->method('isDue')
            ->with($this->dateTime)
            ->willReturn(false);

        static::assertEquals($this->job, $this->job->run($this->dateTime));
    }

    /**
     * @return void
     */
    public function testRunWithoutDateTime(): void
    {
        $this->cronExpressionMock->expects(static::atLeastOnce())
            ->method('isDue')
            ->withAnyParameters()
            ->willReturn(true);

        static::assertEquals($this->job, $this->job->run());
    }

    /**
     * @return void
     */
    public function testIsRun(): void
    {
        $this->processMock->expects(static::atLeastOnce())
            ->method('isRunning')
            ->willReturn(true);

        static::assertTrue($this->job->isRunning());
    }
}
