<?php

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Exception\NotSupportedTypeException;

class EventDispatcherTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventQueueProducerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventQueueProducerMock;

    /**
     * @var \Jellyfish\Event\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerMock;

    /**
     * @var \Jellyfish\Event\EventInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMock;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

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

        $this->eventDispatcher = new EventDispatcher($this->eventQueueProducerMock);
    }

    /**
     * @return void
     */
    public function testAddAndRemoveListener(): void
    {
        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('testListener');

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_ASYNC);

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->addListener($this->eventName, $this->eventListenerMock)
        );

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->removeListener($this->eventName, $this->eventListenerMock)
        );

        $hasListener = $this->eventDispatcher->hasListener(
            EventListenerInterface::TYPE_ASYNC,
            $this->eventName,
            'testListener'
        );

        $this->assertFalse($hasListener);
    }

    /**
     * @return void
     */
    public function testHasListener(): void
    {
        $hasListener = $this->eventDispatcher->hasListener(
            EventListenerInterface::TYPE_ASYNC,
            $this->eventName,
            'testListener'
        );

        $this->assertFalse($hasListener);
    }

    /**
     * @return void
     */
    public function testGetNonExistingListener(): void
    {
        $listener = $this->eventDispatcher->getListener(
            EventListenerInterface::TYPE_ASYNC,
            $this->eventName,
            'testListener'
        );

        $this->assertNull($listener);
    }

    /**
     * @return void
     */
    public function testRemoveNonExistingListener(): void
    {
        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('testListener');

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_ASYNC);

        $result = $this->eventDispatcher->removeListener(
            $this->eventName,
            $this->eventListenerMock
        );

        $this->assertEquals($this->eventDispatcher, $result);
    }

    /**
     * @return void
     */
    public function testAddAndGetListener(): void
    {
        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('testListener');

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_ASYNC);

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->addListener($this->eventName, $this->eventListenerMock)
        );

        $listener = $this->eventDispatcher->getListener(
            EventListenerInterface::TYPE_ASYNC,
            $this->eventName,
            'testListener'
        );

        $this->assertEquals($this->eventListenerMock, $listener);
    }

    /**
     * @return void
     */
    public function testGetListeners(): void
    {
        $listeners = $this->eventDispatcher->getListeners();

        $this->assertIsArray($listeners);
        $this->assertArrayHasKey(EventListenerInterface::TYPE_ASYNC, $listeners);
        $this->assertArrayHasKey(EventListenerInterface::TYPE_SYNC, $listeners);
    }

    /**
     * @return void
     */
    public function testGetAsyncListeners(): void
    {
        $listeners = $this->eventDispatcher->getListeners(EventListenerInterface::TYPE_ASYNC);

        $this->assertIsArray($listeners);
        $this->assertCount(0, $listeners);
    }

    /**
     * @return void
     */
    public function testGetXListeners(): void
    {
        try {
            $this->eventDispatcher->getListeners('x');
            $this->fail();
        } catch (NotSupportedTypeException $e) {
        }
    }

    /**
     * @return void
     */
    public function testDispatchWithoutListeners(): void
    {
        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($this->eventName);

        $result = $this->eventDispatcher->dispatch($this->eventMock);

        $this->assertEquals($this->eventDispatcher, $result);
    }

    /**
     * @return void
     */
    public function testDispatchSyncListeners(): void
    {
        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('testListener');

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_SYNC);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($this->eventName);

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->addListener($this->eventName, $this->eventListenerMock)
        );

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('handle')
            ->with($this->eventMock);

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->dispatch($this->eventMock)
        );
    }

    public function testDispatchAsyncListeners(): void
    {
        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('testListener');

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_ASYNC);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($this->eventName);

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->addListener($this->eventName, $this->eventListenerMock)
        );

        $this->eventQueueProducerMock->expects($this->atLeastOnce())
            ->method('enqueueEvent')
            ->with($this->eventName, $this->eventMock, $this->eventListenerMock);

        $this->assertEquals(
            $this->eventDispatcher,
            $this->eventDispatcher->dispatch($this->eventMock)
        );
    }
}
