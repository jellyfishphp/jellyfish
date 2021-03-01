<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class QueueConsumerTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    protected $connectionMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\QueueRabbitMq\MessageMapperInterface
     */
    protected $messageMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\PhpAmqpLib\Channel\AMQPChannel
     */
    protected $amqpChannelMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\MessageInterface
     */
    protected $messageMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\PhpAmqpLib\Message\AMQPMessage
     */
    protected $amqpMessageMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\DestinationInterface
     */
    protected $destinationMock;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var string
     */
    protected $json;

    /**
     * @var \Jellyfish\QueueRabbitMq\ConsumerInterface
     */
    protected $queueConsumer;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMapperMock = $this->getMockBuilder(MessageMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->amqpChannelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->amqpMessageMock = $this->getMockBuilder(AMQPMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueName = 'Foo';
        $this->json = '{...}';

        $this->queueConsumer = new QueueConsumer(
            $this->connectionMock,
            $this->messageMapperMock
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueue')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturn($this->amqpMessageMock);

        $this->amqpMessageMock->expects(static::atLeastOnce())
            ->method('getBody')
            ->willReturn($this->json);

        $this->messageMapperMock->expects(static::atLeastOnce())
            ->method('fromJson')
            ->with($this->json)
            ->willReturn($this->messageMock);

        static::assertEquals(
            $this->messageMock,
            $this->queueConsumer->receiveMessage($this->destinationMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromEmptyQueue(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueue')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturn(null);

        $this->amqpMessageMock->expects(static::never())
            ->method('getBody');

        $this->messageMapperMock->expects(static::never())
            ->method('fromJson')
            ->with(static::anything());

        static::assertEquals(
            null,
            $this->queueConsumer->receiveMessage($this->destinationMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessages(): void
    {
        $messageMocks = [$this->messageMock];

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueue')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturnOnConsecutiveCalls($this->amqpMessageMock, null);

        $this->amqpMessageMock->expects(static::atLeastOnce())
            ->method('getBody')
            ->willReturn($this->json);

        $this->messageMapperMock->expects(static::atLeastOnce())
            ->method('fromJson')
            ->with($this->json)
            ->willReturn($this->messageMock);

        static::assertEquals(
            $messageMocks,
            $this->queueConsumer->receiveMessages($this->destinationMock, 1)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessagesBelowCount(): void
    {
        $messageMocks = [$this->messageMock];

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueue')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturnOnConsecutiveCalls($this->amqpMessageMock, null);

        $this->amqpMessageMock->expects(static::once())
            ->method('getBody')
            ->willReturn($this->json);

        $this->messageMapperMock->expects(static::once())
            ->method('fromJson')
            ->with($this->json)
            ->willReturn($this->messageMock);

        static::assertEquals(
            $messageMocks,
            $this->queueConsumer->receiveMessages($this->destinationMock, 2)
        );
    }
}
