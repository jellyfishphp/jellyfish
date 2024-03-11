<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use Exception;
use Jellyfish\Queue\ConsumerInterface;
use Jellyfish\Queue\Destination;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\ProducerInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;

class QueueClientTest extends Unit
{
    protected MockObject&ConsumerInterface $consumerMock;

    protected MockObject&ProducerInterface $producerMock;

    protected DestinationInterface&MockObject $destinationMock;

    protected MessageInterface&MockObject $messageMock;

    protected QueueClient $queueClient;

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
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(self::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn($this->messageMock);

        $this->assertEquals($this->messageMock, $this->queueClient->receiveMessage($this->destinationMock));
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromEmptyDestination(): void
    {
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(self::atLeastOnce())
            ->method('receiveMessage')
            ->with($this->destinationMock)
            ->willReturn(null);

        $this->assertEquals(null, $this->queueClient->receiveMessage($this->destinationMock));
    }

    /**
     * @return void
     */
    public function testReceiveMessageWithNotExistingConsumer(): void
    {
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        try {
            $this->queueClient->receiveMessage($this->destinationMock);
            self::fail();
        } catch (Exception) {
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

        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(self::atLeastOnce())
            ->method('receiveMessages')
            ->with($this->destinationMock)
            ->willReturn($messages);

        $this->assertEquals($messages, $this->queueClient->receiveMessages($this->destinationMock, 10));
    }

    /**
     * @return void
     */
    public function testReceiveMessagesFromEmptyDestination(): void
    {
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->consumerMock->expects(self::atLeastOnce())
            ->method('receiveMessages')
            ->with($this->destinationMock)
            ->willReturn([]);

        $this->assertSame([], $this->queueClient->receiveMessages($this->destinationMock, 10));
    }

    /**
     * @return void
     */
    public function testReceiveMessagesWithNotExistingConsumer(): void
    {
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_FANOUT);

        try {
            $this->queueClient->receiveMessages($this->destinationMock, 10);
            self::fail();
        } catch (Exception) {
        }
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $this->destinationMock->expects(self::atLeastOnce())
            ->method('getType')
            ->willReturn(DestinationInterface::TYPE_QUEUE);

        $this->producerMock->expects(self::atLeastOnce())
            ->method('sendMessage')
            ->with($this->destinationMock, $this->messageMock)
            ->willReturn($this->producerMock);

        $this->assertEquals($this->queueClient, $this->queueClient->sendMessage($this->destinationMock, $this->messageMock));
    }

    /**
     * @return void
     */
    public function testSendMessageWithNotExistingProducer(): void
    {
        try {
            $this->destinationMock->expects(self::atLeastOnce())
                ->method('getType')
                ->willReturn(DestinationInterface::TYPE_FANOUT);

            $this->queueClient->sendMessage($this->destinationMock, $this->messageMock);
            self::fail();
        } catch (Exception) {
        }
    }

    /**
     * @return void
     */
    public function testSetConsumer(): void
    {
        /** @var \Jellyfish\Queue\ConsumerInterface&\PHPUnit\Framework\MockObject\MockObject $consumerMock */
        $consumerMock = $this->getMockBuilder(ConsumerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEquals($this->queueClient, $this->queueClient->setConsumer(DestinationInterface::TYPE_FANOUT, $consumerMock));
    }

    /**
     * @return void
     */
    public function testSetProducer(): void
    {
        /** @var \Jellyfish\Queue\ProducerInterface&\PHPUnit\Framework\MockObject\MockObject $producerMock */
        $producerMock = $this->getMockBuilder(ProducerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertEquals($this->queueClient, $this->queueClient->setProducer(DestinationInterface::TYPE_FANOUT, $producerMock));
    }
}
