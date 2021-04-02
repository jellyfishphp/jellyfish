<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class ActivityMonitorFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFacadeMock;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorFactory
     */
    protected $activityMonitorFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->processFacadeMock = $this->getMockBuilder(ProcessFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->activityMonitorFactory = new ActivityMonitorFactory(
            $this->processFacadeMock,
            $this->serializerFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testGetActivityReader(): void
    {
        static::assertInstanceOf(
            ActivityReader::class,
            $this->activityMonitorFactory->getActivityReader()
        );
    }

    /**
     * @return void
     */
    public function testGetPropertyNameConverter(): void
    {
        static::assertInstanceOf(
            PropertyNameConverter::class,
            $this->activityMonitorFactory->getPropertyNameConverter()
        );
    }

    /**
     * @return void
     */
    public function testGetActivityManager(): void
    {
        static::assertInstanceOf(
            ActivityManager::class,
            $this->activityMonitorFactory->getActivityManager()
        );
    }
}
