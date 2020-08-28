<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueClientTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\QueueClientInterface
     */
    protected $queueClient;

    /**
     * @var \PhpAmqpLib\Connection\AbstractConnection|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $connectionMock;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $channelMock;

    /**
     * @var \Jellyfish\Queue\MessageMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMapperMock;

    /**
     * @var \Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $amqpMessageFactoryMock;

    /**
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;

    /**
     * @var \PhpAmqpLib\Message\AMQPMessage|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $amqpMessageMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->channelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->amqpMessageFactoryMock = $this->getMockBuilder(AmqpMessageFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMapperMock = $this->getMockBuilder(MessageMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->amqpMessageMock = $this->getMockBuilder(AMQPMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->amqpMessageMock->delivery_info['channel'] = $this->channelMock;
        $this->amqpMessageMock->delivery_info['delivery_tag'] = '';

        $this->queueClient = new QueueClient(
            $this->connectionMock,
            $this->amqpMessageFactoryMock,
            $this->messageMapperMock
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $queueName = 'foo';
        $expectedMessageAsJson = '{"foo": "bar"}';

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('channel')
            ->willReturn($this->channelMock);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName, false, true);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_get')
            ->with($queueName, true)
            ->willReturn($this->amqpMessageMock);

        $this->amqpMessageMock->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn($expectedMessageAsJson);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('fromJson')
            ->with($expectedMessageAsJson)
            ->willReturn($this->messageMock);

        $this->assertEquals($this->messageMock, $this->queueClient->receiveMessage($queueName));
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromEmptyQueue(): void
    {
        $queueName = 'foo';

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('channel')
            ->willReturn($this->channelMock);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName, false, true);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_get')
            ->with($queueName, true)
            ->willReturn(null);

        $this->messageMapperMock->expects($this->never())
            ->method('fromJson');

        $this->assertNull($this->queueClient->receiveMessage($queueName));
    }

    /**
     * @return void
     */
    public function testReceiveMessagesBelowCount(): void
    {
        $queueName = 'foo';
        $count = 100;
        $expectedMessageAsJson = '{"foo": "bar"}';

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('channel')
            ->willReturn($this->channelMock);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName, false, true);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_get')
            ->with($queueName, true)
            ->willReturnOnConsecutiveCalls($this->amqpMessageMock, null);

        $this->amqpMessageMock->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn($expectedMessageAsJson);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('fromJson')
            ->with($expectedMessageAsJson)
            ->willReturn($this->messageMock);


        $this->assertCount(1, $this->queueClient->receiveMessages($queueName, $count));
    }

    /**
     * @return void
     */
    public function testReceiveMessages(): void
    {
        $queueName = 'foo';
        $count = 2;
        $expectedMessageAsJson = '{"foo": "bar"}';

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('channel')
            ->willReturn($this->channelMock);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName, false, true);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_get')
            ->with($queueName, true)
            ->willReturnOnConsecutiveCalls($this->amqpMessageMock, $this->amqpMessageMock);

        $this->amqpMessageMock->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn($expectedMessageAsJson);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('fromJson')
            ->with($expectedMessageAsJson)
            ->willReturn($this->messageMock);


        $this->assertCount(2, $this->queueClient->receiveMessages($queueName, $count));
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $queueName = 'foo';
        $messageAsJson = '{"foo": "bar"}';

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('channel')
            ->willReturn($this->channelMock);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('toJson')
            ->with($this->messageMock)
            ->willReturn($messageAsJson);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName, false, true);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_publish')
            ->with($this->isInstanceOf(AMQPMessage::class), '', $queueName);

        $this->queueClient->sendMessage($queueName, $this->messageMock);
    }

    /**
     * @return void
     */
    public function testDestructWithClosedConnection(): void
    {
        $this->connectionMock->expects($this->atLeastOnce())
            ->method('isConnected')
            ->willReturn(false);

        $this->connectionMock->expects($this->never())
            ->method('close');

        unset($this->queueClient);
    }

    /**
     * @return void
     */
    public function testDestruct(): void
    {
        $this->connectionMock->expects($this->atLeastOnce())
            ->method('isConnected')
            ->willReturn(true);

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('close');

        unset($this->queueClient);
    }
}
