<?php

declare(strict_types=1);

namespace Jellyfish\ActivityMonitor;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessConstants;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Pimple\Container;

class ActivityMonitorServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected Container $container;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @var \Jellyfish\ActivityMonitor\ActivityMonitorServiceProvider
     */
    protected ActivityMonitorServiceProvider $activityMonitorServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container->offsetSet(SerializerConstants::FACADE, static fn() => $self->serializerFacadeMock);

        $this->container->offsetSet(ProcessConstants::FACADE, static fn() => $self->getMockBuilder(ProcessFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock());

        $this->activityMonitorServiceProvider = new ActivityMonitorServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('addPropertyNameConverterStrategy')
            ->willReturn($this->serializerFacadeMock);

        $this->activityMonitorServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(ActivityMonitorConstants::FACADE));
        static::assertInstanceOf(
            ActivityMonitorFacadeInterface::class,
            $this->container->offsetGet(ActivityMonitorConstants::FACADE)
        );
    }
}
