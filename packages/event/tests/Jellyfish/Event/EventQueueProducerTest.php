<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationFactoryInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueClientInterface;
use PHPUnit\Framework\MockObject\MockObject;

class EventQueueProducerTest extends Unit
{
    protected EventMapperInterface&MockObject $eventMapperMock;

    protected MockObject&DestinationFactoryInterface $destinationFactoryMock;

    protected DestinationInterface&MockObject $destinationMock;

    protected MockObject&QueueClientInterface $queueClientMock;

    protected MockObject&EventInterface $eventMock;

    protected MockObject&EventListenerInterface $eventListenerMock;

    protected MessageInterface&MockObject $messageMock;

    protected EventQueueProducerInterface $eventQueueProducer;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->eventMapperMock = $this->getMockBuilder(EventMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueClientMock = $this->getMockBuilder(QueueClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationFactoryMock = $this->getMockBuilder(DestinationFactoryInterface::class)
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
            $this->queueClientMock,
            $this->destinationFactoryMock,
        );
    }

    /**
     * @return void
     */
    public function testEnqueueEvent(): void
    {
        $eventName = 'test';

        $this->eventMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn('test');

        $this->destinationFactoryMock->expects(self::atLeastOnce())
            ->method('create')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setName')
            ->with($eventName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->eventMapperMock->expects(self::atLeastOnce())
            ->method('toMessage')
            ->with($this->eventMock)
            ->willReturn($this->messageMock);

        $this->queueClientMock->expects(self::atLeastOnce())
            ->method('sendMessage')
            ->with($this->destinationMock, $this->messageMock);

        $this->assertEquals($this->eventQueueProducer, $this->eventQueueProducer->enqueue($this->eventMock));
    }
}
