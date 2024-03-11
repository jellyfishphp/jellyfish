<?php

declare(strict_types=1);

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Cron\CronExpression;
use DateTime;
use Jellyfish\Process\ProcessInterface;
use PHPUnit\Framework\MockObject\MockObject;

class JobTest extends Unit
{
    protected CronExpression&MockObject $cronExpressionMock;

    protected ProcessInterface&MockObject $processMock;

    protected DateTime $dateTime;

    protected Job $job;

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

        $this->processMock->expects($this->atLeastOnce())
            ->method('getCommand')
            ->willReturn($expectedCommand);

        $this->assertEquals($expectedCommand, $this->job->getCommand());
    }

    /**
     * @return void
     */
    public function testGetCronExpression(): void
    {
        $this->assertEquals($this->cronExpressionMock, $this->job->getCronExpression());
    }

    /**
     * @return void
     */
    public function testRun(): void
    {
        $this->cronExpressionMock->expects($this->atLeastOnce())
            ->method('isDue')
            ->with($this->dateTime)
            ->willReturn(true);

        $this->assertEquals($this->job, $this->job->run($this->dateTime));
    }

    /**
     * @return void
     */
    public function testRunWithoutDueDateTime(): void
    {
        $this->cronExpressionMock->expects($this->atLeastOnce())
            ->method('isDue')
            ->with($this->dateTime)
            ->willReturn(false);

        $this->assertEquals($this->job, $this->job->run($this->dateTime));
    }

    /**
     * @return void
     */
    public function testRunWithoutDateTime(): void
    {
        $this->cronExpressionMock->expects($this->atLeastOnce())
            ->method('isDue')
            ->withAnyParameters()
            ->willReturn(true);

        $this->assertEquals($this->job, $this->job->run());
    }

    /**
     * @return void
     */
    public function testIsRun(): void
    {
        $this->processMock->expects($this->atLeastOnce())
            ->method('isRunning')
            ->willReturn(true);

        $this->assertTrue($this->job->isRunning());
    }
}
