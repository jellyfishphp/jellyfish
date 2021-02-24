<?php

namespace Jellyfish\EventCache;

use Codeception\Test\Unit;
use Jellyfish\Cache\CacheConstants;
use Jellyfish\Cache\CacheFacadeInterface;
use Jellyfish\Event\EventConstants;
use Jellyfish\EventCache\EventErrorHandler\CacheEventErrorHandler;
use Jellyfish\Serializer\SerializerConstants;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Pimple\Container;

class EventCacheServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Cache\CacheFacadeInterface
     */
    protected $cacheFacadeMock;

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

        $self = $this;

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheFacadeMock = $this->getMockBuilder(CacheFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new Container();

        $this->container->offsetSet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static function () {
            return [];
        });

        $this->container->offsetSet(CacheConstants::FACADE, static function () use ($self) {
            return $self->cacheFacadeMock;
        });

        $this->container->offsetSet(SerializerConstants::FACADE, static function () use ($self) {
            return $self->serializerFacadeMock;
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
