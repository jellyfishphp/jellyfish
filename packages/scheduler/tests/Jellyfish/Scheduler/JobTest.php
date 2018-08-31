<?php

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Cron\CronExpression;
use DateTime;
use org\bovigo\vfs\vfsStream;

class JobTest extends Unit
{
    /**
     * @var \Jellyfish\Scheduler\JobInterface
     */
    protected $job;

    /**
     * @var \Cron\CronExpression|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $cronExpressionMock;

    /**
     * @var string
     */
    protected $tempDir;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->tempDir = vfsStream::setup('tmp')->url();
        $this->tempDir = rtrim($this->tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->dateTime = new DateTime();

        $this->cronExpressionMock = $this->getMockBuilder(CronExpression::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->job = new Job(
            'ls -la',
            $this->cronExpressionMock,
            $this->tempDir
        );
    }

    /**
     * @return void
     */
    public function testGetId(): void
    {
        $this->assertEquals(sha1('ls -la'), $this->job->getId());
    }

    /**
     * @return void
     */
    public function testGetCommand(): void
    {
        $this->assertEquals('ls -la', $this->job->getCommand());
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

        $this->job->run($this->dateTime);
    }

    /**
     * @return void
     */
    public function testRunWithExistingLockFile(): void
    {
        touch($this->tempDir . $this->job->getId());

        $this->cronExpressionMock->expects($this->never())
            ->method('isDue')
            ->with($this->dateTime)
            ->willReturn(true);

        $this->job->run($this->dateTime);

        unlink($this->tempDir . $this->job->getId());
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

        $this->job->run($this->dateTime);
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

        $this->job->run();
    }
}
