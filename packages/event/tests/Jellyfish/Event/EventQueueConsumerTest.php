<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Codeception\Test\Unit;
use Jellyfish\Event\Command\EventQueueConsumeCommand;
use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Process\ProcessInterface;
use Jellyfish\Queue\DestinationFactoryInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueClientInterface;

use function sprintf;

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
     * @var \Jellyfish\Queue\DestinationFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $destinationFactoryMock;

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

        $this->destinationFactoryMock = $this->getMockBuilder(DestinationFactoryInterface::class)
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
            $this->processFactoryMock,
            $this->eventMapperMock,
            $this->eventQueueNameGeneratorMock,
            $this->queueClientMock,
            $this->destinationFactoryMock,
            '/'
        );
    }

    /**
     * @return void
     */
    public function testDequeue(): void
    {
        $this->eventQueueNameGeneratorMock->expects(self::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->destinationFactoryMock->expects(self::atLeastOnce())
            ->method('create')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setName')
            ->with($this->eventQueueName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setProperty')
            ->with('bind', $this->eventName)
            ->willReturn($this->destinationMock);

        $this->queueClientMock->expects(self::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn($this->messageMock);

        $this->eventMapperMock->expects(self::atLeastOnce())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $event = $this->eventQueueConsumer->dequeue(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        self::assertEquals($this->eventMock, $event);
    }

    /**
     * @return void
     */
    public function testDequeueFromEmptyQueue(): void
    {
        $this->eventQueueNameGeneratorMock->expects(self::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->destinationFactoryMock->expects(self::atLeastOnce())
            ->method('create')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setName')
            ->with($this->eventQueueName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setProperty')
            ->with('bind', $this->eventName)
            ->willReturn($this->destinationMock);

        $this->queueClientMock->expects(self::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn(null);

        $this->eventMapperMock->expects(self::never())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $event = $this->eventQueueConsumer->dequeue(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        self::assertNull($event);
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

        $this->eventQueueNameGeneratorMock->expects(self::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->processFactoryMock->expects(self::atLeastOnce())
            ->method('create')
            ->with($command)
            ->willReturn($this->processMock);

        $this->processMock->expects(self::atLeastOnce())
            ->method('start')
            ->willReturn($this->processMock);

        $result = $this->eventQueueConsumer->dequeueAsProcess(
            $this->eventName,
            $this->eventListenerIdentifier
        );

        self::assertEquals($this->eventQueueConsumer, $result);
    }

    /**
     * @return void
     */
    public function testDequeueBulk(): void
    {
        $chunkSize = 100;
        $messages = [$this->messageMock];

        $this->eventQueueNameGeneratorMock->expects(self::atLeastOnce())
            ->method('generate')
            ->with($this->eventName, $this->eventListenerIdentifier)
            ->willReturn($this->eventQueueName);

        $this->destinationFactoryMock->expects(self::atLeastOnce())
            ->method('create')
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setName')
            ->with($this->eventQueueName)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->destinationMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('setProperty')
            ->with('bind', $this->eventName)
            ->willReturn($this->destinationMock);

        $this->queueClientMock->expects(self::atLeastOnce())
            ->method('receiveMessages')
            ->with($this->destinationMock, $chunkSize)
            ->willReturn($messages);

        $this->eventMapperMock->expects(self::atLeastOnce())
            ->method('fromMessage')
            ->with($this->messageMock)
            ->willReturn($this->eventMock);

        $events = $this->eventQueueConsumer->dequeueBulk(
            $this->eventName,
            $this->eventListenerIdentifier,
            $chunkSize
        );

        self::assertCount(1, $events);
        self::assertEquals($this->eventMock, $events[0]);
    }
}
