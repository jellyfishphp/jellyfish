<?php

namespace Jellyfish\EventCache;

use Codeception\Test\Unit;
use Jellyfish\Cache\CacheConstants;
use Jellyfish\Cache\CacheInterface;
use Jellyfish\Event\EventConstants;
use Jellyfish\EventCache\EventErrorHandler\CacheEventErrorHandler;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;

class EventCacheServiceProviderTest extends Unit
{
    protected SerializerInterface&MockObject $serializerMock;

    protected CacheInterface&MockObject $cacheMock;

    protected Container $container;

    protected EventCacheServiceProvider $eventCacheServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $self = $this;

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new Container();

        $this->container->offsetSet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static function () {
            return [];
        });

        $this->container->offsetSet(CacheConstants::CONTAINER_KEY_CACHE, static function () use ($self) {
            return $self->cacheMock;
        });

        $this->container->offsetSet(SerializerConstants::CONTAINER_KEY_SERIALIZER, static function () use ($self) {
            return $self->serializerMock;
        });

        $this->eventCacheServiceProvider = new EventCacheServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->eventCacheServiceProvider->register($this->container);

        self::assertCount(
            1,
            $this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)
        );

        self::assertInstanceOf(
            CacheEventErrorHandler::class,
            $this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)[0]
        );
    }

    /**
     * @return void
     */
    public function testRegisterWithoutPredefinedDefaultEventErrorHandlers(): void
    {
        $this->container->offsetUnset(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS);

        $this->eventCacheServiceProvider->register($this->container);

        self::assertFalse($this->container->offsetExists(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));
    }
}
