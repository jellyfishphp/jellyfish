<?php

namespace Jellyfish\EventLog;

use Codeception\Test\Unit;
use Jellyfish\Event\EventServiceProvider;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Log\LogServiceProvider;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class EventLogServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected $loggerMock;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Pimple\ServiceProviderInterface
     */
    protected $eventLogServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $self = $this;

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new Container();

        $this->container->offsetSet(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static function () {
            return [];
        });

        $this->container->offsetSet(LogServiceProvider::CONTAINER_KEY_LOGGER, static function () use ($self) {
            return $self->loggerMock;
        });

        $this->eventLogServiceProvider = new EventLogServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->eventLogServiceProvider->register($this->container);

        self::assertCount(
            1,
            $this->container->offsetGet(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)
        );

        self::assertInstanceOf(
            LogEventErrorHandler::class,
            $this->container->offsetGet(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)[0]
        );
    }

    /**
     * @return void
     */
    public function testRegisterWithoutPredefinedDefaultEventErrorHandlers(): void
    {
        $this->container->offsetUnset(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS);

        $this->eventLogServiceProvider->register($this->container);

        self::assertInstanceOf(
            LoggerInterface::class,
            $this->container->offsetGet(LogServiceProvider::CONTAINER_KEY_LOGGER)
        );

        self::assertFalse($this->container->offsetExists(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));
    }
}
