<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;

class QueueProducerTest extends Unit
{
    protected ConnectionInterface&MockObject $connectionMock;

    protected MockObject&MessageMapperInterface $messageMapperMock;

    protected AMQPChannel&MockObject $amqpChannelMock;

    protected MessageInterface&MockObject $messageMock;

    protected MockObject&AMQPMessage $amqpMessageMock;

    protected AmqpMessageFactoryInterface&MockObject $amqpMessageFactoryMock;

    protected DestinationInterface&MockObject $destinationMock;

    protected string $queueName;

    protected string $json;

    protected QueueProducer $queueProducer;

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

        $this->amqpMessageFactoryMock = $this->getMockBuilder(AmqpMessageFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueName = 'Foo';
        $this->json = '{...}';

        $this->queueProducer = new QueueProducer(
            $this->connectionMock,
            $this->messageMapperMock,
            $this->amqpMessageFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $this->messageMapperMock->expects(self::atLeastOnce())
            ->method('toJson')
            ->with($this->messageMock)
            ->willReturn($this->json);

        $this->amqpMessageFactoryMock->expects(self::atLeastOnce())
            ->method('create')
            ->with($this->json)
            ->willReturn($this->amqpMessageMock);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('createQueue')
            ->with($this->destinationMock)
            ->willReturn($this->connectionMock);

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn($this->queueName);

        $this->connectionMock->expects(self::atLeastOnce())
            ->method('getChannel')
            ->willReturn($this->amqpChannelMock);

        $this->amqpChannelMock->expects(self::atLeastOnce())
            ->method('basic_publish')
            ->with($this->amqpMessageMock, '', $this->queueName);

        $this->assertEquals($this->queueProducer, $this->queueProducer->sendMessage($this->destinationMock, $this->messageMock));
    }
}
