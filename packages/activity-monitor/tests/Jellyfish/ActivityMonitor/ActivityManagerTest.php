<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Codeception\Test\Unit;

class ActivityManagerTest extends Unit
{
    /**
     * @var \Jellyfish\ActivityMonitor\Pm2Interface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $pm2Mock;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityManager
     */
    protected ActivityManager $activityManager;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->pm2Mock = $this->getMockBuilder(Pm2Interface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityManager = new ActivityManager($this->pm2Mock);
    }

    /**
     * @return void
     */
    public function testStart(): void
    {
        $activityId = 1;

        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('startActivity')
            ->with($activityId)
            ->willReturn($this->pm2Mock);

        static::assertEquals(
            $this->activityManager,
            $this->activityManager->start($activityId)
        );
    }

    /**
     * @return void
     */
    public function testStop(): void
    {
        $activityId = 1;

        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('stopActivity')
            ->with($activityId)
            ->willReturn($this->pm2Mock);

        static::assertEquals(
            $this->activityManager,
            $this->activityManager->stop($activityId)
        );
    }

    /**
     * @return void
     */
    public function testRestart(): void
    {
        $activityId = 1;

        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('restartActivity')
            ->with($activityId)
            ->willReturn($this->pm2Mock);

        static::assertEquals(
            $this->activityManager,
            $this->activityManager->restart($activityId)
        );
    }

    /**
     * @return void
     */
    public function testStartAll(): void
    {
        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('restartAllActivities')
            ->willReturn($this->pm2Mock);

        static::assertEquals(
            $this->activityManager,
            $this->activityManager->startAll()
        );
    }

    /**
     * @return void
     */
    public function testRestartAll(): void
    {
        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('restartAllActivities')
            ->willReturn($this->pm2Mock);

        static::assertEquals(
            $this->activityManager,
            $this->activityManager->restartAll()
        );
    }

    /**
     * @return void
     */
    public function testStopAll(): void
    {
        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('stopAllActivities')
            ->willReturn($this->pm2Mock);

        static::assertEquals(
            $this->activityManager,
            $this->activityManager->stopAll()
        );
    }
}
