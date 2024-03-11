<?php

namespace Jellyfish\EventLog;

use Codeception\Test\Unit;
use Jellyfish\Event\EventConstants;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Log\LogConstants;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class EventLogServiceProviderTest extends Unit
{
    protected MockObject&LoggerInterface $loggerMock;

    protected Container $container;

    protected EventLogServiceProvider $eventLogServiceProvider;

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

        $this->container->offsetSet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static fn(): array => []);

        $this->container->offsetSet(LogConstants::CONTAINER_KEY_LOGGER, static fn(): MockObject&LoggerInterface => $self->loggerMock);

        $this->eventLogServiceProvider = new EventLogServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->eventLogServiceProvider->register($this->container);

        $this->assertCount(1, $this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));

        $this->assertInstanceOf(LogEventErrorHandler::class, $this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)[0]);
    }

    /**
     * @return void
     */
    public function testRegisterWithoutPredefinedDefaultEventErrorHandlers(): void
    {
        $this->container->offsetUnset(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS);

        $this->eventLogServiceProvider->register($this->container);

        $this->assertFalse($this->container->offsetExists(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));
    }
}
