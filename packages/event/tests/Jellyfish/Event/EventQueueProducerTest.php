<?php

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueClientInterface;

class EventQueueProducerTest extends Unit
{
    /**
     * @var \Jellyfish\Event\EventMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMapperMock;

    /**
     * @var \Jellyfish\Event\EventQueueNameGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventQueueNameGeneratorMock;

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

        $this->eventQueueNameGeneratorMock = $this->getMockBuilder(EventQueueNameGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueClientMock = $this->getMockBuilder(QueueClientInterface::class)
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
            $this->eventQueueNameGeneratorMock,
            $this->queueClientMock
        );
    }

    /**
     * @return void
     */
    public function testEnqueueEvent(): void
    {
        $eventName = 'test';
        $listenerIdentifier = 'testListener';
        $eventQueueName = sprintf('%s_%s', $eventName, $listenerIdentifier);

        $this->eventListenerMock->expects($this->atLeastOnce())
            ->method('getIdentifier')
            ->willReturn($listenerIdentifier);

        $this->eventQueueNameGeneratorMock->expects($this->atLeastOnce())
            ->method('generate')
            ->with($eventName, $listenerIdentifier)
            ->willReturn($eventQueueName);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('test');

        $this->eventMapperMock->expects($this->atLeastOnce())
            ->method('toMessage')
            ->with($this->eventMock)
            ->willReturn($this->messageMock);

        $this->queueClientMock->expects($this->atLeastOnce())
            ->method('sendMessage')
            ->with($eventQueueName, $this->messageMock);

        $this->assertEquals(
            $this->eventQueueProducer,
            $this->eventQueueProducer->enqueueEvent($this->eventMock, $this->eventListenerMock)
        );
    }
}
