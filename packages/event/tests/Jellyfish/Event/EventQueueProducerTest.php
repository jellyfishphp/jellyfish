<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationFactoryInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueClientInterface;

class EventQueueProducerTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\DestinationFactoryInterface
     */
    protected $destinationFactoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\DestinationInterface
     */
    protected $destinationMock;

    /**
     * @var \Jellyfish\Queue\QueueClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueClientMock;

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
            $this->destinationFactoryMock
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

        self::assertEquals(
            $this->eventQueueProducer,
            $this->eventQueueProducer->enqueue($this->eventMock)
        );
    }
}
