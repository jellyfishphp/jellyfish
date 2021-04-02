<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Codeception\Test\Unit;
use Generated\Transfer\ActivityMonitor\Activity;
use Generated\Transfer\Pm2\Activity as Pm2Activity;

class ActivityReaderTest extends Unit
{
    /**
     * @var \Jellyfish\ActivityMonitor\Pm2Interface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $pm2Mock;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $activityMapperMock;

    /**
     * @var \Generated\Transfer\Pm2\Activity[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected $pm2ActivityMocks;

    /**
     * @var \Generated\Transfer\ActivityMonitor\Activity[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected $activityMocks;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityReader
     */
    protected $activityReader;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->pm2Mock = $this->getMockBuilder(Pm2Interface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityMapperMock = $this->getMockBuilder(ActivityMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pm2ActivityMocks = [
            $this->getMockBuilder(Pm2Activity::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->activityMocks = [
            $this->getMockBuilder(Activity::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->activityReader = new ActivityReader($this->pm2Mock, $this->activityMapperMock);
    }

    /**
     * @return void
     */
    public function testGetAll(): void
    {
        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('getActivities')
            ->willReturn($this->pm2ActivityMocks);

        $this->activityMapperMock->expects(static::atLeastOnce())
            ->method('mapPm2ActivitiesToActivities')
            ->with($this->pm2ActivityMocks)
            ->willReturn($this->activityMocks);

        static::assertEquals(
            $this->activityMocks,
            $this->activityReader->getAll()
        );
    }

    /**
     * @return void
     */
    public function testGetById(): void
    {
        $activityId = 1;

        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('getActivities')
            ->willReturn($this->pm2ActivityMocks);

        $this->activityMapperMock->expects(static::atLeastOnce())
            ->method('mapPm2ActivitiesToActivities')
            ->with($this->pm2ActivityMocks)
            ->willReturn($this->activityMocks);

        $this->activityMocks[0]->expects(static::atLeastOnce())
            ->method('getId')
            ->willReturn($activityId);

        static::assertEquals(
            $this->activityMocks[0],
            $this->activityReader->getById($activityId)
        );
    }

    /**
     * @return void
     */
    public function testGetByIdWithInvalidId(): void
    {
        $this->pm2Mock->expects(static::atLeastOnce())
            ->method('getActivities')
            ->willReturn($this->pm2ActivityMocks);

        $this->activityMapperMock->expects(static::atLeastOnce())
            ->method('mapPm2ActivitiesToActivities')
            ->with($this->pm2ActivityMocks)
            ->willReturn($this->activityMocks);

        $this->activityMocks[0]->expects(static::atLeastOnce())
            ->method('getId')
            ->willReturn(2);

        static::assertEquals(
            null,
            $this->activityReader->getById(1)
        );
    }
}
