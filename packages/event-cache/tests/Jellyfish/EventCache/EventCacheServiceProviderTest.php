<?php

declare(strict_types=1);

namespace Jellyfish\EventCache;

use Codeception\Test\Unit;
use Jellyfish\Cache\CacheConstants;
use Jellyfish\Cache\CacheFacadeInterface;
use Jellyfish\Event\EventConstants;
use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\EventCache\EventErrorHandler\CacheEventErrorHandler;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Pimple\Container;

class EventCacheServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFacadeMock;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Pimple\ServiceProviderInterface
     */
    protected $eventCacheServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventFacadeMock = $this->getMockBuilder(EventFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet(EventConstants::FACADE, static function () use ($self) {
            return $self->eventFacadeMock;
        });

        $this->container->offsetSet(CacheConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(CacheFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->container->offsetSet(SerializerConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(SerializerFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->eventCacheServiceProvider = new EventCacheServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('addDefaultEventErrorHandler')
            ->with(
                static::callback(
                    static function (EventErrorHandlerInterface $eventErrorHandler) {
                        return $eventErrorHandler instanceof CacheEventErrorHandler;
                    }
                )
            )->willReturn($this->eventFacadeMock);

        $this->eventCacheServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(EventConstants::FACADE));
        static::assertInstanceOf(
            EventFacadeInterface::class,
            $this->container->offsetGet(EventConstants::FACADE)
        );
    }
}
