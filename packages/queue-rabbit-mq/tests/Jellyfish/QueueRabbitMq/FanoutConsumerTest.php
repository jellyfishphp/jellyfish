<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class FanoutConsumerTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    protected $connectionMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\MessageMapperInterface
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
    protected $propertyName;

    /**
     * @var string
     */
    protected $json;

    /**
     * @var \Jellyfish\Queue\ConsumerInterface
     */
    protected $fanoutConsumer;

    /**
     * @var int
     */
    protected $count;

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
        $this->propertyName = 'Bar';
        $this->json = '{...}';
        $this->count = 0;

        $this->fanoutConsumer = new FanoutConsumer(
            $this->connectionMock,
            $this->messageMapperMock
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(self::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturn($this->amqpMessageMock);

        $this->amqpMessageMock->expects(self::atLeastOnce())
            ->method('getBody')
            ->willReturn($this->json);

        $this->messageMapperMock->expects(self::atLeastOnce())
            ->method('fromJson')
            ->with($this->json)
            ->willReturn($this->messageMock);

        self::assertEquals(
            $this->messageMock,
            $this->fanoutConsumer->receiveMessage($this->destinationMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromEmptyQueue(): void
    {
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(self::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturn(null);

        $this->amqpMessageMock->expects(self::never())
            ->method('getBody');

        $this->messageMapperMock->expects(self::never())
            ->method('fromJson')
            ->with(self::anything());

        self::assertEquals(
            null,
            $this->fanoutConsumer->receiveMessage($this->destinationMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessages(): void
    {
        $messageMocks = [$this->messageMock];

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(self::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturnOnConsecutiveCalls($this->amqpMessageMock, null);

        $this->amqpMessageMock->expects(self::atLeastOnce())
            ->method('getBody')
            ->willReturn($this->json);

        $this->messageMapperMock->expects(self::atLeastOnce())
            ->method('fromJson')
            ->with($this->json)
            ->willReturn($this->messageMock);

        self::assertEquals(
            $messageMocks,
            $this->fanoutConsumer->receiveMessages($this->destinationMock, 1)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessagesBelowCount(): void
    {
        $messageMocks = [$this->messageMock];

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(self::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturnOnConsecutiveCalls($this->amqpMessageMock, null);

        $this->amqpMessageMock->expects(self::once())
            ->method('getBody')
            ->willReturn($this->json);

        $this->messageMapperMock->expects(self::once())
            ->method('fromJson')
            ->with($this->json)
            ->willReturn($this->messageMock);

        self::assertEquals(
            $messageMocks,
            $this->fanoutConsumer->receiveMessages($this->destinationMock, 2)
        );
    }

    /**
     * @return void
     */
    public function testCreateQueueAndBind(): void
    {
        $self = $this;

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getProperty')
            ->willReturn($this->propertyName);

        $this->destinationMock->expects(self::exactly(1))
            ->method('setName')
            ->willReturn($this->destinationMock);

        $this->connectionMock->expects(self::exactly(2))
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->will($this->returnCallback(static function () use ($self) {
                if ($self->count === 0) {
                    $self->count++;
                    throw new \Exception('test', 404);
                }
                return $self->connectionMock;
            }));

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('createExchange');

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(self::atLeastOnce())
            ->method('basic_get')
            ->with($this->queueName, true)
            ->willReturn($this->amqpMessageMock);

        $this->amqpMessageMock->expects(self::atLeastOnce())
            ->method('getBody')
            ->willReturn($this->json);

        $this->messageMapperMock->expects(self::atLeastOnce())
            ->method('fromJson')
            ->with($this->json)
            ->willReturn($this->messageMock);

        self::assertEquals(
            $this->messageMock,
            $this->fanoutConsumer->receiveMessage($this->destinationMock)
        );
    }
}
