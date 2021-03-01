<?php

declare(strict_types=1);

namespace Jellyfish\EventLog;

use Codeception\Test\Unit;
use Jellyfish\Event\EventConstants;
use Jellyfish\Event\EventErrorHandlerInterface;
use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\EventLog\EventErrorHandler\LogEventErrorHandler;
use Jellyfish\Log\LogConstants;
use Jellyfish\Log\LogFacadeInterface;
use Pimple\Container;

class EventLogServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFacadeMock;

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

        $this->eventFacadeMock = $this->getMockBuilder(EventFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet(EventConstants::FACADE, static function () use ($self) {
            return $self->eventFacadeMock;
        });

        $this->container->offsetSet(LogConstants::FACADE, static function () use ($self) {
            return $self->getMockBuilder(LogFacadeInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->eventLogServiceProvider = new EventLogServiceProvider();
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
                        return $eventErrorHandler instanceof LogEventErrorHandler;
                    }
                )
            )->willReturn($this->eventFacadeMock);

        $this->eventLogServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(EventConstants::FACADE));
        static::assertInstanceOf(
            EventFacadeInterface::class,
            $this->container->offsetGet(EventConstants::FACADE)
        );
    }
}
