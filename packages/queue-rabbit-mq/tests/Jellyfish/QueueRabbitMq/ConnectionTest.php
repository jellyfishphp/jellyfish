<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Queue\DestinationInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;

use function strtolower;

class ConnectionTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\PhpAmqpLib\Connection\AbstractConnection
     */
    protected $amqpConnectionMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\PhpAmqpLib\Channel\AMQPChannel
     */
    protected $amqpChannelMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jellyfish\Queue\DestinationInterface
     */
    protected $destinationMock;

    /**
     * @var \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    protected $connection;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->amqpConnectionMock = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->amqpChannelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection = new Connection($this->amqpConnectionMock);
    }

    /**
     * @return void
     */
    public function testGetChannel(): void
    {
        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        static::assertEquals($this->amqpChannelMock, $this->connection->getChannel());
    }

    /**
     * @return void
     */
    public function testGetChannelTwoTimes(): void
    {
        $this->amqpConnectionMock->expects(static::once())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        static::assertEquals($this->amqpChannelMock, $this->connection->getChannel());
    }

    /**
     * @return void
     */
    public function testCreateQueue(): void
    {
        $destinationName = 'Foo';

        $this->amqpConnectionMock->expects(static::once())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($destinationName);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('queue_declare')
            ->with($destinationName, false, true, false, false);

        static::assertEquals($this->connection, $this->connection->createQueue($this->destinationMock));
    }

    /**
     * @return void
     */
    public function testCreateQueueAndBind(): void
    {
        $destinationName = 'Foo';
        $destinationPropertyBind = 'Bar';

        $this->amqpConnectionMock->expects(static::once())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($destinationName);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getProperty')
            ->with('bind')
            ->willReturn($destinationPropertyBind);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('queue_declare')
            ->with($destinationName, false, true, false, false);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('queue_bind')
            ->with($destinationName, $destinationPropertyBind);

        static::assertEquals($this->connection, $this->connection->createQueueAndBind($this->destinationMock));
    }

    /**
     * @return void
     */
    public function testCreateQueueAndBindWithError(): void
    {
        $destinationName = 'Foo';

        $this->amqpConnectionMock->expects(static::once())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($destinationName);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getProperty')
            ->with('bind')
            ->willReturn(null);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('queue_declare')
            ->with($destinationName, false, true, false, false);

        $this->amqpChannelMock->expects(static::never())
            ->method('queue_bind')
            ->with($destinationName, static::anything());

        try {
            $this->connection->createQueueAndBind($this->destinationMock);
            static::fail();
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    public function testCreateExchange(): void
    {
        $destinationName = 'Foo';

        $this->amqpConnectionMock->expects(static::once())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($destinationName);

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        $this->amqpChannelMock->expects(static::atLeastOnce())
            ->method('exchange_declare')
            ->with($destinationName, strtolower(DestinationInterface::TYPE_FANOUT), false, true, false);

        static::assertEquals($this->connection, $this->connection->createExchange($this->destinationMock));
    }

    /**
     * @return void
     */
    public function testClose(): void
    {
        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        static::assertEquals($this->amqpChannelMock, $this->connection->getChannel());

        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('isConnected')
            ->willReturn(true);

        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('close');

        static::assertEquals($this->connection, $this->connection->close());
    }

    /**
     * @return void
     */
    public function testDestruct(): void
    {
        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        static::assertEquals($this->amqpChannelMock, $this->connection->getChannel());

        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('isConnected')
            ->willReturn(true);

        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('close');

        unset($this->connection);
    }

    /**
     * @return void
     */
    public function testDestructWithError(): void
    {
        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('channel')
            ->willReturn($this->amqpChannelMock);

        static::assertEquals($this->amqpChannelMock, $this->connection->getChannel());

        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('isConnected')
            ->willReturn(true);

        $this->amqpConnectionMock->expects(static::atLeastOnce())
            ->method('close')
            ->willThrowException(new Exception('Foo'));

        unset($this->connection);
    }
}
