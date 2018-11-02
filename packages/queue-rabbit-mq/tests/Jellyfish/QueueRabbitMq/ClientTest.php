<?php

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ClientTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\ClientInterface
     */
    protected $client;

    /**
     * @var \PhpAmqpLib\Connection\AbstractConnection|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $connectionMock;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $channelMock;

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

        $this->client = new Client($this->connectionMock);
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $queueName = 'foo';
        $expectedMessage = '{"foo": "bar"}';

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_get')
            ->with($queueName)
            ->willReturn($expectedMessage);

        $this->assertEquals($expectedMessage, $this->client->receiveMessage($queueName));
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $queueName = 'foo';
        $message = '{"foo": "bar"}';

        $this->channelMock->expects($this->atLeastOnce())
            ->method('queue_declare')
            ->with($queueName);

        $this->channelMock->expects($this->atLeastOnce())
            ->method('basic_publish')
            ->with($this->isInstanceOf(AMQPMessage::class), '', $queueName);

        $this->client->sendMessage($queueName, $message);
    }

    public function testDestruct()
    {
        $this->channelMock->expects($this->atLeastOnce())
            ->method('close');

        $this->connectionMock->expects($this->atLeastOnce())
            ->method('close');

        unset($this->client);
    }
}
