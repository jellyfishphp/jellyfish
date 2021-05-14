<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;

class EventFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFactoryMock;

    /**
     * @var \Jellyfish\Event\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventDispatcherMock;

    /**
     * @var \Jellyfish\Event\EventListenerProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerProviderMock;

    /**
     * @var \Jellyfish\Event\Event|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMock;

    /**
     * @var \Jellyfish\Event\EventErrorHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventErrorHandlerMock;

    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerMock;

    /**
     * @var \Jellyfish\Event\EventQueueWorker|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventQueueWorkerMock;

    /**
     * @var \Jellyfish\Event\EventQueueConsumerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventQueueConsumerMock;

    /**
     * @var \Jellyfish\Event\EventErrorHandlerProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventErrorHandlerProviderMock;

    /**
     * @var \Jellyfish\Event\EventFacade
     */
    protected EventFacade $eventFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventFactoryMock = $this->getMockBuilder(EventFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerProviderMock = $this->getMockBuilder(EventListenerProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventErrorHandlerMock = $this->getMockBuilder(EventErrorHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerMock = $this->getMockBuilder(EventListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueWorkerMock = $this->getMockBuilder(EventQueueWorker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueConsumerMock = $this->getMockBuilder(EventQueueConsumerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventErrorHandlerProviderMock = $this->getMockBuilder(EventErrorHandlerProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventFacade = new EventFacade($this->eventFactoryMock);
    }

    /**
     * @return void
     */
    public function testCreateEvent(): void
    {
        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('createEvent')
            ->willReturn($this->eventMock);

        static::assertEquals(
            $this->eventMock,
            $this->eventFacade->createEvent()
        );
    }

    /**
     * @return void
     */
    public function testDispatchEvent(): void
    {
        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('getEventDispatcher')
            ->willReturn($this->eventDispatcherMock);

        $this->eventDispatcherMock->expects(static::atLeastOnce())
            ->method('dispatch')
            ->with($this->eventMock)
            ->willReturn($this->eventDispatcherMock);

        static::assertEquals(
            $this->eventFacade,
            $this->eventFacade->dispatchEvent($this->eventMock)
        );
    }

    /**
     * @return void
     */
    public function testAddEventListener(): void
    {
        $eventName = 'Foo';

        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('getEventListenerProvider')
            ->willReturn($this->eventListenerProviderMock);

        $this->eventListenerProviderMock->expects(static::atLeastOnce())
            ->method('addListener')
            ->with($eventName, $this->eventListenerMock)
            ->willReturn($this->eventListenerProviderMock);

        static::assertEquals(
            $this->eventFacade,
            $this->eventFacade->addEventListener($eventName, $this->eventListenerMock)
        );
    }

    /**
     * @return void
     */
    public function testStartEventQueueWorker(): void
    {
        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('createEventQueueWorker')
            ->willReturn($this->eventQueueWorkerMock);

        $this->eventQueueWorkerMock->expects(static::atLeastOnce())
            ->method('start');

        static::assertEquals(
            $this->eventFacade,
            $this->eventFacade->startEventQueueWorker()
        );
    }

    /**
     * @return void
     */
    public function testGetEventListener(): void
    {
        $type = EventListenerInterface::TYPE_SYNC;
        $eventName = 'Foo';
        $listenerIdentifier = 'Bar';

        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('getEventListenerProvider')
            ->willReturn($this->eventListenerProviderMock);

        $this->eventListenerProviderMock->expects(static::atLeastOnce())
            ->method('getListener')
            ->with($type, $eventName, $listenerIdentifier)
            ->willReturn($this->eventListenerMock);

        static::assertEquals(
            $this->eventListenerMock,
            $this->eventFacade->getEventListener($type, $eventName, $listenerIdentifier)
        );
    }

    /**
     * @return void
     */
    public function testDequeueEvent(): void
    {
        $eventName = 'Foo';
        $listenerIdentifier = 'Bar';

        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('getEventQueueConsumer')
            ->willReturn($this->eventQueueConsumerMock);

        $this->eventQueueConsumerMock->expects(static::atLeastOnce())
            ->method('dequeue')
            ->with($eventName, $listenerIdentifier)
            ->willReturn($this->eventMock);

        static::assertEquals(
            $this->eventMock,
            $this->eventFacade->dequeueEvent($eventName, $listenerIdentifier)
        );
    }

    /**
     * @return void
     */
    public function testDequeueEventBulk(): void
    {
        $eventName = 'Foo';
        $listenerIdentifier = 'Bar';
        $chunkSize = 100;

        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('getEventQueueConsumer')
            ->willReturn($this->eventQueueConsumerMock);

        $this->eventQueueConsumerMock->expects(static::atLeastOnce())
            ->method('dequeueBulk')
            ->with($eventName, $listenerIdentifier, $chunkSize)
            ->willReturn([$this->eventMock]);

        static::assertEquals(
            [$this->eventMock],
            $this->eventFacade->dequeueEventBulk($eventName, $listenerIdentifier, $chunkSize)
        );
    }

    /**
     * @return void
     */
    public function testAddDefaultEventErrorHandler(): void
    {
        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('getDefaultEventErrorHandlerProvider')
            ->willReturn($this->eventErrorHandlerProviderMock);

        $this->eventErrorHandlerProviderMock->expects(static::atLeastOnce())
            ->method('add')
            ->with($this->eventErrorHandlerMock)
            ->willReturn($this->eventErrorHandlerProviderMock);

        static::assertEquals(
            $this->eventFacade,
            $this->eventFacade->addDefaultEventErrorHandler($this->eventErrorHandlerMock)
        );
    }

    /**
     * @return void
     */
    public function testGetDefaultEventErrorHandlers(): void
    {
        $this->eventFactoryMock->expects(static::atLeastOnce())
            ->method('getDefaultEventErrorHandlerProvider')
            ->willReturn($this->eventErrorHandlerProviderMock);

        $this->eventErrorHandlerProviderMock->expects(static::atLeastOnce())
            ->method('getAll')
            ->willReturn([$this->eventErrorHandlerMock]);

        static::assertEquals(
            [$this->eventErrorHandlerMock],
            $this->eventFacade->getDefaultEventErrorHandlers()
        );
    }
}
