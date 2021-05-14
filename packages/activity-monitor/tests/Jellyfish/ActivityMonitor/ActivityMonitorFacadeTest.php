<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Codeception\Test\Unit;
use Generated\Transfer\ActivityMonitor\Activity;

class ActivityMonitorFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $activityMonitorFactoryMock;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $activityManagerMock;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $activityReaderMock;

    /**
     * @var \Jellyfish\ActivityMonitor\PropertyNameConverterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $propertyNameConverterMock;

    /**
     * @var \Generated\Transfer\ActivityMonitor\Activity[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected array $activityMocks;

    /**
     * @var string
     */
    protected string $format;

    /**
     * @var string
     */
    protected string $class;

    /**
     * @var string
     */
    protected string $camelCasedPropertyName;

    /**
     * @var string
     */
    protected string $snakeCasedPropertyName;

    /**
     * @var int
     */
    protected int $activityId;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorFacade
     */
    protected ActivityMonitorFacade $activityMonitorFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->activityMonitorFactoryMock = $this->getMockBuilder(ActivityMonitorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityManagerMock = $this->getMockBuilder(ActivityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityReaderMock = $this->getMockBuilder(ActivityReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyNameConverterMock = $this->getMockBuilder(PropertyNameConverterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityMocks = [
            $this->getMockBuilder(Activity::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->format = 'json';
        $this->class = 'Generated\\Transfer\\Pm2\\FooBar';
        $this->camelCasedPropertyName = 'fooBarId';
        $this->snakeCasedPropertyName = 'foo_bar_id';
        $this->activityId = 1;

        $this->activityMonitorFacade = new ActivityMonitorFacade(
            $this->activityMonitorFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testGetAllActivities(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getActivityReader')
            ->willReturn($this->activityReaderMock);

        $this->activityReaderMock->expects(static::atLeastOnce())
            ->method('getAll')
            ->willReturn($this->activityMocks);

        static::assertEquals(
            $this->activityMocks,
            $this->activityMonitorFacade->getAllActivities()
        );
    }

    /**
     * @return void
     */
    public function testConvertPropertyNameAfterNormalize(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getPropertyNameConverter')
            ->willReturn($this->propertyNameConverterMock);

        $this->propertyNameConverterMock->expects(static::atLeastOnce())
            ->method('convertAfterNormalize')
            ->with($this->camelCasedPropertyName, $this->class, $this->format)
            ->willReturn($this->snakeCasedPropertyName);

        static::assertEquals(
            $this->snakeCasedPropertyName,
            $this->activityMonitorFacade->convertPropertyNameAfterNormalize(
                $this->camelCasedPropertyName,
                $this->class,
                $this->format
            )
        );
    }

    /**
     * @return void
     */
    public function testConvertPropertyNameAfterDenormalize(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getPropertyNameConverter')
            ->willReturn($this->propertyNameConverterMock);

        $this->propertyNameConverterMock->expects(static::atLeastOnce())
            ->method('convertAfterDenormalize')
            ->with($this->snakeCasedPropertyName, $this->class, $this->format)
            ->willReturn($this->camelCasedPropertyName);

        static::assertEquals(
            $this->camelCasedPropertyName,
            $this->activityMonitorFacade->convertPropertyNameAfterDenormalize(
                $this->snakeCasedPropertyName,
                $this->class,
                $this->format
            )
        );
    }

    /**
     * @return void
     */
    public function testStartActivity(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getActivityManager')
            ->willReturn($this->activityManagerMock);

        $this->activityManagerMock->expects(static::atLeastOnce())
            ->method('start')
            ->with($this->activityId)
            ->willReturn($this->activityManagerMock);

        static::assertEquals(
            $this->activityMonitorFacade,
            $this->activityMonitorFacade->startActivity($this->activityId)
        );
    }

    /**
     * @return void
     */
    public function testRestartActivity(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getActivityManager')
            ->willReturn($this->activityManagerMock);

        $this->activityManagerMock->expects(static::atLeastOnce())
            ->method('restart')
            ->with($this->activityId)
            ->willReturn($this->activityManagerMock);

        static::assertEquals(
            $this->activityMonitorFacade,
            $this->activityMonitorFacade->restartActivity($this->activityId)
        );
    }

    /**
     * @return void
     */
    public function testStopActivity(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getActivityManager')
            ->willReturn($this->activityManagerMock);

        $this->activityManagerMock->expects(static::atLeastOnce())
            ->method('stop')
            ->with($this->activityId)
            ->willReturn($this->activityManagerMock);

        static::assertEquals(
            $this->activityMonitorFacade,
            $this->activityMonitorFacade->stopActivity($this->activityId)
        );
    }

    /**
     * @return void
     */
    public function testStartAllActivities(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getActivityManager')
            ->willReturn($this->activityManagerMock);

        $this->activityManagerMock->expects(static::atLeastOnce())
            ->method('startAll')
            ->willReturn($this->activityManagerMock);

        static::assertEquals(
            $this->activityMonitorFacade,
            $this->activityMonitorFacade->startAllActivities()
        );
    }

    /**
     * @return void
     */
    public function testRestartAllActivities(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getActivityManager')
            ->willReturn($this->activityManagerMock);

        $this->activityManagerMock->expects(static::atLeastOnce())
            ->method('restartAll')
            ->willReturn($this->activityManagerMock);

        static::assertEquals(
            $this->activityMonitorFacade,
            $this->activityMonitorFacade->restartAllActivities()
        );
    }

    /**
     * @return void
     */
    public function testStopAllActivities(): void
    {
        $this->activityMonitorFactoryMock->expects(static::atLeastOnce())
            ->method('getActivityManager')
            ->willReturn($this->activityManagerMock);

        $this->activityManagerMock->expects(static::atLeastOnce())
            ->method('stopAll')
            ->willReturn($this->activityManagerMock);

        static::assertEquals(
            $this->activityMonitorFacade,
            $this->activityMonitorFacade->stopAllActivities()
        );
    }
}
