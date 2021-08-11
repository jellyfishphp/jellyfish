<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Process\ProcessInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueFacadeInterface;

use function sprintf;

class EventQueueConsumerTest extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $processFacadeMock;

    /**
     * @var \Jellyfish\Event\EventMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventMapperMock;

    /**
     * @var \Jellyfish\Event\EventQueueNameGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventQueueNameGeneratorMock;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueFacadeMock;

    /**
     * @var \Jellyfish\Queue\DestinationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $destinationMock;

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
    protected EventQueueConsumerInterface $eventQueueConsumer;

    /**
     * @var string
     */
    protected string $eventName;

    /**
     * @var string
     */
    protected string $eventListenerIdentifier;

    /**
     * @var string
     */
    protected string $eventQueueName;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->processFacadeMock = $this->getMockBuilder(ProcessFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMapperMock = $this->getMockBuilder(EventMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventQueueNameGeneratorMock = $this->getMockBuilder(EventQueueNameGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueFacadeMock = $this->getMockBuilder(QueueFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
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
        $this->eventQueueName = sprintf('%s_%s', $this->eventName, $this->eventListenerIdentifier);

        $this->eventQueueConsumer = new EventQueueConsumer(
            $this->processFacadeMock,
            $this->eventMapperMock,
            $this->eventQueueNameGeneratorMock,
            $this->queueFacadeMock,
            '/'
        );
    }

    /**
     * @return void
     */
    public function testDequeue(): void
    {
        $this->eventQueueNameGeneratorMock->expects(static::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('createDestination')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($this->eventQueueName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setProperty')
            ->with('bind', $this->eventName)
            ->willReturn($this->destinationMock);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn($this->messageMock);

        $this->eventMapperMock->expects(static::atLeastOnce())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $event = $this->eventQueueConsumer->dequeue(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        static::assertEquals($this->eventMock, $event);
    }

    /**
     * @return void
     */
    public function testDequeueFromEmptyQueue(): void
    {
        $this->eventQueueNameGeneratorMock->expects(static::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('createDestination')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($this->eventQueueName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setProperty')
            ->with('bind', $this->eventName)
            ->willReturn($this->destinationMock);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn(null);

        $this->eventMapperMock->expects(static::never())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $event = $this->eventQueueConsumer->dequeue(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        static::assertNull($event);
    }

    /**
     * @return void
     */
    public function testDequeueAsProcess(): void
    {
        $command = [
            '/vendor/bin/console',
            EventQueueConsumeCommand::NAME,
            $this->eventName,
            $this->eventListenerIdentifier
        ];

        $this->eventQueueNameGeneratorMock->expects(static::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->processFacadeMock->expects(static::atLeastOnce())
            ->method('createProcess')
            ->with($command)
            ->willReturn($this->processMock);

        $this->processMock->expects(static::atLeastOnce())
            ->method('isRunning')
            ->willReturn(false);

        $this->processMock->expects(static::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $result = $this->eventQueueConsumer->dequeueAsProcess(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        static::assertEquals($this->eventQueueConsumer, $result);
    }

    /**
     * @return void
     */
    public function testDequeueBulk(): void
    {
        $chunkSize = 100;
        $messages = [$this->messageMock];

        $this->eventQueueNameGeneratorMock->expects(static::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('createDestination')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($this->eventQueueName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('setProperty')
            ->with('bind', $this->eventName)
            ->willReturn($this->destinationMock);

        $this->queueFacadeMock->expects(static::atLeastOnce())
            ->method('receiveMessages')
            ->with($this->destinationMock, $chunkSize)
            ->willReturn($messages);

        $this->eventMapperMock->expects(static::atLeastOnce())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $events = $this->eventQueueConsumer->dequeueBulk(
            $this->eventName,
            $this->eventListenerIdentifier,
            $chunkSize
        );

        static::assertCount(1, $events);
        static::assertEquals($this->eventMock, $events[0]);
    }
}
