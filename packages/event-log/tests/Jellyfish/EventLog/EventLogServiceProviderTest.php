<?php

namespace Jellyfish\EventLog;

use Codeception\Test\Unit;
use Jellyfish\Event\EventConstants;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Log\LogConstants;
use Jellyfish\Log\LogFacadeInterface;
use Pimple\Container;

class EventLogServiceProviderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Log\LogFacadeInterface
     */
    protected $logFacadeMock;

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

        $this->logFacadeMock = $this->getMockBuilder(LogFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new Container();

        $this->container->offsetSet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static function () {
            return [];
        });

        $this->container->offsetSet(LogConstants::FACADE, static function () use ($self) {
            return $self->logFacadeMock;
        });

        $this->eventLogServiceProvider = new EventLogServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->eventLogServiceProvider->register($this->container);

        static::assertCount(
            1,
            $this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)
        );

        static::assertInstanceOf(
            LogEventErrorHandler::class,
            $this->container->offsetGet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS)[0]
        );
    }

    /**
     * @return void
     */
    public function testRegisterWithoutPredefinedDefaultEventErrorHandlers(): void
    {
        $this->container->offsetUnset(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS);

        $this->eventLogServiceProvider->register($this->container);

        static::assertFalse($this->container->offsetExists(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS));
    }
}
