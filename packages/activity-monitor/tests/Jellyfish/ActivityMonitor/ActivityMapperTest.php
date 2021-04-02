<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Codeception\Test\Unit;
use Generated\Transfer\Pm2\Activity;
use Generated\Transfer\Pm2\Activity\Environment;

class ActivityMapperTest extends Unit
{
    /**
     * @var \Generated\Transfer\Pm2\Activity|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $pm2ActivityMock;

    /**
     * @var \Generated\Transfer\Pm2\Activity\Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $pm2ActivityEnvironmentMock;

    /**
     * @var int
     */
    protected $activityId;

    /**
     * @var int
     */
    protected $activityPid;

    /**
     * @var string
     */
    protected $activityName;

    /**
     * @var string
     */
    protected $activityStatus;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMapper
     */
    protected $activityMapper;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->pm2ActivityMock = $this->getMockBuilder(Activity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pm2ActivityEnvironmentMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityId = 1;
        $this->activityPid = 1;
        $this->activityName = 'foo';
        $this->activityStatus = 'online';

        $this->activityMapper = new ActivityMapper();
    }

    /**
     * @return void
     */
    public function testMapPm2ActivityToActivity(): void
    {
        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getPmId')
            ->willReturn($this->activityId);

        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getPid')
            ->willReturn($this->activityPid);

        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->activityName);

        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getPm2Env')
            ->willReturn($this->pm2ActivityEnvironmentMock);

        $this->pm2ActivityEnvironmentMock->expects(static::atLeastOnce())
            ->method('getStatus')
            ->willReturn($this->activityStatus);

        $activity = $this->activityMapper->mapPm2ActivityToActivity($this->pm2ActivityMock);

        static::assertEquals($this->activityId, $activity->getId());
        static::assertEquals($this->activityPid, $activity->getProcessId());
        static::assertEquals($this->activityName, $activity->getName());
        static::assertEquals($this->activityStatus, $activity->getStatus());
    }

    /**
     * @return void
     */
    public function testMapPm2ActivitiesToActivities(): void
    {
        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getPmId')
            ->willReturn($this->activityId);

        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getPid')
            ->willReturn($this->activityPid);

        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->activityName);

        $this->pm2ActivityMock->expects(static::atLeastOnce())
            ->method('getPm2Env')
            ->willReturn($this->pm2ActivityEnvironmentMock);

        $this->pm2ActivityEnvironmentMock->expects(static::atLeastOnce())
            ->method('getStatus')
            ->willReturn($this->activityStatus);

        $activities = $this->activityMapper->mapPm2ActivitiesToActivities([$this->pm2ActivityMock]);

        static::assertCount(1, $activities);
        static::assertEquals($this->activityId, $activities[0]->getId());
        static::assertEquals($this->activityPid, $activities[0]->getProcessId());
        static::assertEquals($this->activityName, $activities[0]->getName());
        static::assertEquals($this->activityStatus, $activities[0]->getStatus());
    }
}
