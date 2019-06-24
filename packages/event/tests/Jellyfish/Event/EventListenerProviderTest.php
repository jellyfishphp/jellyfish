<?php

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Exception\NotSupportedTypeException;

class EventListenerProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventListenerProviderInterface
     */
    protected $eventListenerProvider;

    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerMock;

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

        $this->eventListenerMock = $this->getMockBuilder(EventListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventName = 'test';

        $this->eventListenerProvider = new EventListenerProvider();
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
            $this->eventListenerProvider,
            $this->eventListenerProvider->addListener($this->eventName, $this->eventListenerMock)
        );

        $this->assertEquals(
            $this->eventListenerProvider,
            $this->eventListenerProvider->removeListener($this->eventName, $this->eventListenerMock)
        );

        $hasListener = $this->eventListenerProvider->hasListener(
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
        $hasListener = $this->eventListenerProvider->hasListener(
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
        $listener = $this->eventListenerProvider->getListener(
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

        $result = $this->eventListenerProvider->removeListener(
            $this->eventName,
            $this->eventListenerMock
        );

        $this->assertEquals($this->eventListenerProvider, $result);
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
            $this->eventListenerProvider,
            $this->eventListenerProvider->addListener($this->eventName, $this->eventListenerMock)
        );

        $listener = $this->eventListenerProvider->getListener(
            EventListenerInterface::TYPE_ASYNC,
            $this->eventName,
            'testListener'
        );

        $this->assertEquals($this->eventListenerMock, $listener);
    }

    /**
     * @return void
     */
    public function testGetAllListeners(): void
    {
        $listeners = $this->eventListenerProvider->getAllListeners();

        $this->assertIsArray($listeners);
        $this->assertArrayHasKey(EventListenerInterface::TYPE_ASYNC, $listeners);
        $this->assertArrayHasKey(EventListenerInterface::TYPE_SYNC, $listeners);
    }

    /**
     * @return void
     */
    public function testGetAsyncListeners(): void
    {
        $listeners = $this->eventListenerProvider->getListenersByType(EventListenerInterface::TYPE_ASYNC);

        $this->assertIsArray($listeners);
        $this->assertCount(0, $listeners);
    }

    /**
     * @return void
     */
    public function testGetXListeners(): void
    {
        try {
            $this->eventListenerProvider->getListenersByType('x');
            $this->fail();
        } catch (NotSupportedTypeException $e) {
        }
    }

    /**
     * @return void
     */
    public function testGetListenersByTypeAndEventName(): void
    {
        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn('testListener');

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn(EventListenerInterface::TYPE_ASYNC);

        $this->assertEquals(
            $this->eventListenerProvider,
            $this->eventListenerProvider->addListener($this->eventName, $this->eventListenerMock)
        );

        $listeners = $this->eventListenerProvider->getListenersByTypeAndEventName(
            EventListenerInterface::TYPE_ASYNC,
            $this->eventName
        );

        $this->assertIsArray($listeners);
        $this->assertCount(1, $listeners);
    }

    /**
     * @return void
     */
    public function testGetListenersByTypeAndEventNameWithEmptyResult(): void
    {
        $listeners = $this->eventListenerProvider->getListenersByTypeAndEventName(
            EventListenerInterface::TYPE_ASYNC,
            $this->eventName
        );

        $this->assertIsArray($listeners);
        $this->assertCount(0, $listeners);
    }
}
