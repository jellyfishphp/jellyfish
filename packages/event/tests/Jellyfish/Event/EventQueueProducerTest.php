<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueFacadeInterface;

class EventQueueProducerTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\DestinationInterface
     */
    protected $destinationMock;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueFacadeMock;

    /**
     * @var \Jellyfish\Event\EventInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMock;

    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerMock;

    /**
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;


    /**
     * @var \Jellyfish\Event\EventQueueProducerInterface
     */
    protected $eventQueueProducer;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventMapperMock = $this->getMockBuilder(EventMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueFacadeMock = $this->getMockBuilder(QueueFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerMock = $this->getMockBuilder(EventListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueProducer = new EventQueueProducer(
            $this->eventMapperMock,
            $this->queueFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testEnqueueEvent(): void
    {
        $eventName = 'test';

        $this->eventMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn('test');

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('createDestination')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($eventName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->eventMapperMock->expects(static::atLeastOnce())
            ->method('toMessage')
            ->with($this->eventMock)
            ->willReturn($this->messageMock);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('sendMessage')
            ->with($this->destinationMock, $this->messageMock);

        static::assertEquals(
            $this->eventQueueProducer,
            $this->eventQueueProducer->enqueue($this->eventMock)
        );
    }
}
