<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;

class QueueRabbitMqFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\QueueRabbitMq\QueueClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueClientMock;

    /**
     * @var \Jellyfish\Queue\DestinationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $destinationMock;

    /**
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;

    /**
     * @var \Jellyfish\QueueRabbitMq\QueueRabbitMqFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queueRabbitMqFactoryMock;

    /**
     * @var \Jellyfish\QueueRabbitMq\QueueRabbitMqFacade
     */
    protected $queueRabbitMqFacade;

    /**
     * @Override
     */
    protected function _before(): void
    {
        parent::_before();

        $this->queueClientMock = $this->getMockBuilder(QueueClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueRabbitMqFactoryMock = $this->getMockBuilder(QueueRabbitMqFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueRabbitMqFacade = new QueueRabbitMqFacade($this->queueRabbitMqFactoryMock);
    }

    /**
     * @return void
     */
    public function testCreateDestination(): void
    {
        $this->queueRabbitMqFactoryMock->expects(static::atLeastOnce())
            ->method('createDestination')
            ->willReturn($this->destinationMock);

        static::assertEquals(
            $this->destinationMock,
            $this->queueRabbitMqFacade->createDestination()
        );
    }

    /**
     * @return void
     */
    public function testCreateMessage(): void
    {
        $this->queueRabbitMqFactoryMock->expects(static::atLeastOnce())
            ->method('createMessage')
            ->willReturn($this->messageMock);

        static::assertEquals(
            $this->messageMock,
            $this->queueRabbitMqFacade->createMessage()
        );
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $this->queueRabbitMqFactoryMock->expects(static::atLeastOnce())
            ->method('getQueueClient')
            ->willReturn($this->queueClientMock);

        $this->queueClientMock->expects(static::atLeastOnce())
            ->method('sendMessage')
            ->with($this->destinationMock, $this->messageMock)
            ->willReturn($this->queueClientMock);

        static::assertEquals(
            $this->queueRabbitMqFacade,
            $this->queueRabbitMqFacade->sendMessage($this->destinationMock, $this->messageMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $this->queueRabbitMqFactoryMock->expects(static::atLeastOnce())
            ->method('getQueueClient')
            ->willReturn($this->queueClientMock);

        $this->queueClientMock->expects(static::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn($this->messageMock);

        static::assertEquals(
            $this->messageMock,
            $this->queueRabbitMqFacade->receiveMessage($this->destinationMock)
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessages(): void
    {
        $limit = 10;

        $this->queueRabbitMqFactoryMock->expects(static::atLeastOnce())
            ->method('getQueueClient')
            ->willReturn($this->queueClientMock);

        $this->queueClientMock->expects(static::atLeastOnce())
            ->method('receiveMessages')
            ->with($this->destinationMock, $limit)
            ->willReturn([$this->messageMock]);

        static::assertEquals(
            [$this->messageMock],
            $this->queueRabbitMqFacade->receiveMessages($this->destinationMock, $limit)
        );
    }
}
