<?php

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Process\ProcessInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueClientInterface;

class EventQueueConsumerTest extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFactoryMock;

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
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;

    /**
     * @var \Jellyfish\Event\EventInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMock;

    /**
     * @var \Jellyfish\Process\ProcessInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processMock;

    /**
     * @var \Jellyfish\Event\EventQueueConsumerInterface
     */
    protected $eventQueueConsumer;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var string
     */
    protected $eventListenerIdentifier;

    /**
     * @var string
     */
    protected $eventQueueName;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->processFactoryMock = $this->getMockBuilder(ProcessFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMapperMock = $this->getMockBuilder(EventMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueNameGeneratorMock = $this->getMockBuilder(EventQueueNameGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueClientMock = $this->getMockBuilder(QueueClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processMock = $this->getMockBuilder(ProcessInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventName = 'test';
        $this->eventListenerIdentifier = 'testListener';
        $this->eventQueueName = \sprintf('%s_%s', $this->eventName, $this->eventListenerIdentifier);

        $this->eventQueueConsumer = new EventQueueConsumer(
            $this->processFactoryMock,
            $this->eventMapperMock,
            $this->eventQueueNameGeneratorMock,
            $this->queueClientMock,
            '/'
        );
    }

    /**
     * @return void
     */
    public function testDequeueEvent(): void
    {
        $this->eventQueueNameGeneratorMock->expects($this->atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->queueClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with($this->eventQueueName)
            ->willReturn($this->messageMock);

        $this->eventMapperMock->expects($this->atLeastOnce())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $event = $this->eventQueueConsumer->dequeueEvent(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        $this->assertEquals($this->eventMock, $event);
    }

    /**
     * @return void
     */
    public function testDequeueEventFromEmptyQueue(): void
    {
        $this->eventQueueNameGeneratorMock->expects($this->atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->queueClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with($this->eventQueueName)
            ->willReturn(null);

        $this->eventMapperMock->expects($this->never())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $event = $this->eventQueueConsumer->dequeueEvent(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        $this->assertNull($event);
    }

    /**
     * @return void
     */
    public function testDequeueEventAsProcess(): void
    {
        $command = [
            '/vendor/bin/console',
            EventQueueConsumeCommand::NAME,
            $this->eventName,
            $this->eventListenerIdentifier
        ];

        $this->eventQueueNameGeneratorMock->expects($this->atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->processFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->with($command)
            ->willReturn($this->processMock);

        $this->processMock->expects($this->atLeastOnce())
            ->method('isLocked')
            ->willReturn(false);

        $this->processMock->expects($this->atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $result = $this->eventQueueConsumer->dequeueEventAsProcess(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        $this->assertEquals($this->eventQueueConsumer, $result);
    }
}
