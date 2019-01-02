<?php

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
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        $this->channelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('channel')
            ->willReturn($this->channelMock);

        $this->messageMapperMock = $this->getMockBuilder(MessageMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueClient = new QueueClient($this->connectionMock, $this->messageMapperMock);
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $queueName = 'foo';
        $expectedMessageAsJson = '{"foo": "bar"}';

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_get')
            ->with($queueName)
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

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_get')
            ->with($queueName)
            ->willReturn(null);

        $this->messageMapperMock->expects($this->never())
            ->method('fromJson');

        $this->assertNull($this->queueClient->receiveMessage($queueName));
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $queueName = 'foo';
        $messageAsJson = '{"foo": "bar"}';

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('toJson')
            ->with($this->messageMock)
            ->willReturn($messageAsJson);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_publish')
            ->with($this->isInstanceOf(AMQPMessage::class), '', $queueName);

        $this->queueClient->sendMessage($queueName, $this->messageMock);
    }

    public function testDestruct()
    {
        $this->channelMock->expects($this->atLeastOnce())
            ->method('close');

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('close');

        unset($this->queueClient);
    }
}
