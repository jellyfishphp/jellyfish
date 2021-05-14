<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class FanoutConsumerTest extends Unit
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
     * @var \Jellyfish\QueueRabbitMq\DestinationFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $destinationFactoryMock;

    /**
     * @var \Jellyfish\Queue\DestinationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $exchangeDestinationMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\DestinationInterface
     */
    protected $destinationMock;

    /**
     * @var string
     */
    protected string $queueName;

    /**
     * @var string
     */
    protected string $bind;

    /**
     * @var string
     */
    protected string $propertyName;

    /**
     * @var string
     */
    protected string $json;

    /**
     * @var \Jellyfish\QueueRabbitMq\ConsumerInterface
     */
    protected ConsumerInterface $fanoutConsumer;

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

        $this->destinationFactoryMock = $this->getMockBuilder(DestinationFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exchangeDestinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueName = 'Foo';
        $this->bind = 'FooBar';
        $this->propertyName = 'Bar';
        $this->json = '{...}';

        $this->fanoutConsumer = new FanoutConsumer(
            $this->connectionMock,
            $this->messageMapperMock,
            $this->destinationFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $this->destinationFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getProperty')
            ->with('bind')
            ->willReturn($this->bind);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($this->bind)
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->exchangeDestinationMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createExchange')
            ->with($this->exchangeDestinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

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
            $this->fanoutConsumer->receiveMessage($this->destinationMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessageWithInvalidDestination(): void
    {
        $this->destinationFactoryMock->expects(static::never())
            ->method('create');

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getProperty')
            ->with('bind')
            ->willReturn(null);

        $this->destinationMock->expects(static::never())
            ->method('getType');

        $this->connectionMock->expects(static::never())
            ->method('createExchange');

        $this->connectionMock->expects(static::never())
            ->method('createQueueAndBind');

        $this->destinationMock->expects(static::never())
            ->method('getName');

        $this->connectionMock->expects(static::never())
            ->method('getChannel');

        $this->messageMapperMock->expects(static::never())
            ->method('fromJson');

        try {
            $this->fanoutConsumer->receiveMessage($this->destinationMock);
            static::fail();
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromEmptyQueue(): void
    {
        $this->destinationFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getProperty')
            ->with('bind')
            ->willReturn($this->bind);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($this->bind)
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->exchangeDestinationMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createExchange')
            ->with($this->exchangeDestinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

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
            $this->fanoutConsumer->receiveMessage($this->destinationMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessages(): void
    {
        $messageMocks = [$this->messageMock];

        $this->destinationFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getProperty')
            ->with('bind')
            ->willReturn($this->bind);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($this->bind)
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->exchangeDestinationMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createExchange')
            ->with($this->exchangeDestinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

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
            $this->fanoutConsumer->receiveMessages($this->destinationMock, 1)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessagesBelowCount(): void
    {
        $messageMocks = [$this->messageMock];

        $this->destinationFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getProperty')
            ->with('bind')
            ->willReturn($this->bind);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($this->bind)
            ->willReturn($this->exchangeDestinationMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        $this->exchangeDestinationMock->expects(static::atLeastOnce())
            ->method('setType')
            ->with(DestinationInterface::TYPE_FANOUT)
            ->willReturn($this->exchangeDestinationMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createExchange')
            ->with($this->exchangeDestinationMock)
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects(static::atLeastOnce())
            ->method('createQueueAndBind')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

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
            $this->fanoutConsumer->receiveMessages($this->destinationMock, 2)
        );
    }
}
