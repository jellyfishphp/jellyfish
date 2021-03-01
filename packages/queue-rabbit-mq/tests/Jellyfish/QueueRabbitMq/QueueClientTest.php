<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;

class QueueClientTest extends Unit
{
    /**
     * @var \Jellyfish\QueueRabbitMq\ConsumerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $consumerMock;

    /**
     * @var \Jellyfish\QueueRabbitMq\ProducerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $producerMock;

    /**
     * @var \Jellyfish\Queue\DestinationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $destinationMock;

    /**
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;

    /**
     * @var \Jellyfish\QueueRabbitMq\QueueClientInterface
     */
    protected $queueClient;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->consumerMock = $this->getMockBuilder(ConsumerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->producerMock = $this->getMockBuilder(ProducerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->destinationMock = $this->getMockBuilder(DestinationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueClient = new QueueClient(
            [DestinationInterface::TYPE_QUEUE => $this->consumerMock],
            [DestinationInterface::TYPE_QUEUE => $this->producerMock]
        );
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(static::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn($this->messageMock);

        static::assertEquals($this->messageMock, $this->queueClient->receiveMessage($this->destinationMock));
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromEmptyDestination(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(static::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn(null);

        static::assertEquals(null, $this->queueClient->receiveMessage($this->destinationMock));
    }

    /**
     * @return void
     */
    public function testReceiveMessageWithNotExistingConsumer(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        try {
            $this->queueClient->receiveMessage($this->destinationMock);
            static::fail();
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    public function testReceivesMessages(): void
    {
        $messages = [
            $this->messageMock
        ];

        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(static::atLeastOnce())
            ->method('receiveMessages')
            ->with($this->destinationMock)
            ->willReturn($messages);

        static::assertEquals($messages, $this->queueClient->receiveMessages($this->destinationMock, 10));
    }

    /**
     * @return void
     */
    public function testReceiveMessagesFromEmptyDestination(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(static::atLeastOnce())
            ->method('receiveMessages')
            ->with($this->destinationMock)
            ->willReturn([]);

        static::assertEquals([], $this->queueClient->receiveMessages($this->destinationMock, 10));
    }

    /**
     * @return void
     */
    public function testReceiveMessagesWithNotExistingConsumer(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        try {
            $this->queueClient->receiveMessages($this->destinationMock, 10);
            static::fail();
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $this->destinationMock->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->producerMock->expects(static::atLeastOnce())
            ->method('sendMessage')
            ->with($this->destinationMock, $this->messageMock)
            ->willReturn($this->producerMock);

        static::assertEquals(
            $this->queueClient,
            $this->queueClient->sendMessage($this->destinationMock, $this->messageMock)
        );
    }

    /**
     * @return void
     */
    public function testSendMessageWithNotExistingProducer(): void
    {
        try {
            $this->destinationMock->expects(static::atLeastOnce())
                ->method('getType')
                ->willReturn(DestinationInterface::TYPE_FANOUT);

            $this->queueClient->sendMessage($this->destinationMock, $this->messageMock);
            static::fail();
        } catch (Exception $exception) {
        }
    }

    /**
     * @return void
     */
    public function testSetConsumer(): void
    {
        /** @var \Jellyfish\QueueRabbitMq\ConsumerInterface|\PHPUnit\Framework\MockObject\MockObject $consumerMock */
        $consumerMock = $this->getMockBuilder(ConsumerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        static::assertEquals(
            $this->queueClient,
            $this->queueClient->setConsumer(DestinationInterface::TYPE_FANOUT, $consumerMock)
        );
    }

    /**
     * @return void
     */
    public function testSetProducer(): void
    {
        /** @var \Jellyfish\QueueRabbitMq\ProducerInterface|\PHPUnit\Framework\MockObject\MockObject $producerMock */
        $producerMock = $this->getMockBuilder(ProducerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        static::assertEquals(
            $this->queueClient,
            $this->queueClient->setProducer(DestinationInterface::TYPE_FANOUT, $producerMock)
        );
    }
}
