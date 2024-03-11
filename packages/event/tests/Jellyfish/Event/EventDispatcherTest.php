<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;

class EventDispatcherTest extends Unit
{
    protected MockObject&EventListenerProviderInterface $eventListenerProviderMock;

    protected EventQueueProducerInterface&MockObject $eventQueueProducerMock;

    protected EventListenerInterface&MockObject $eventListenerMock;

    protected EventInterface&MockObject $eventMock;


    protected string $eventName;

    protected EventDispatcher $eventDispatcher;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventListenerProviderMock = $this->getMockBuilder(EventListenerProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueProducerMock = $this->getMockBuilder(EventQueueProducerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerMock = $this->getMockBuilder(EventListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventName = 'test';

        $this->eventDispatcher = new EventDispatcher($this->eventListenerProviderMock, $this->eventQueueProducerMock);
    }

    /**
     * @return void
     */
    public function testDispatchWithoutListeners(): void
    {
        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($this->eventName);

        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListenersByTypeAndEventName')
            ->willReturnCallback(
                fn (string $type, string $eventName): LogicException|array => match([$type, $eventName]) {
                    [
                        EventListenerInterface::TYPE_SYNC,
                        $this->eventName,
                    ] => [],
                    [
                        EventListenerInterface::TYPE_ASYNC,
                        $this->eventName,
                    ] => [],
                    default => new LogicException('Unsupported parameters.')
                },
            );

        $result = $this->eventDispatcher->dispatch($this->eventMock);

        $this->assertEquals($this->eventDispatcher, $result);
    }

    /**
     * @return void
     */
    public function testDispatchSyncListeners(): void
    {
        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($this->eventName);

        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListenersByTypeAndEventName')
            ->willReturnCallback(
                fn (string $type, string $eventName): LogicException|array => match([$type, $eventName]) {
                    [
                        EventListenerInterface::TYPE_SYNC,
                        $this->eventName,
                    ] => [$this->eventListenerMock],
                    [
                        EventListenerInterface::TYPE_ASYNC,
                        $this->eventName,
                    ] => [],
                    default => new LogicException('Unsupported parameters.')
                },
            );

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('handle')
            ->with($this->eventMock);

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->dispatch($this->eventMock),
        );
    }

    /**
     * @return void
     */
    public function testDispatchAsyncListeners(): void
    {
        $this->eventQueueProducerMock->expects($this->atLeastOnce())
            ->method('enqueue')
            ->with($this->eventMock);

        $this->assertEquals($this->eventDispatcher, $this->eventDispatcher->dispatch($this->eventMock));
    }

    /**
     * @return void
     */
    public function testGetEventListenerProvider(): void
    {
        $this->assertEquals(
            $this->eventListenerProviderMock,
            $this->eventDispatcher->getEventListenerProvider(),
        );
    }
}
