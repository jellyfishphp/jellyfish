<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class FanoutProducerTest extends Unit
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
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface
     */
    protected $amqpMessageFactoryMock;

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
     * @var \Jellyfish\QueueRabbitMq\FanoutProducer
     */
    protected $fanoutProducer;

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

        $this->fanoutProducer = new FanoutProducer(
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
            ->method('createExchange')
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
            ->with($this->amqpMessageMock, $this->queueName);

        self::assertEquals(
            $this->fanoutProducer,
            $this->fanoutProducer->sendMessage($this->destinationMock, $this->messageMock)
        );
    }
}
