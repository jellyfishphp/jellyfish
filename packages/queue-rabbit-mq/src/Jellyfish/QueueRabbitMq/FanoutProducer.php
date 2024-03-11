<?php

declare(strict_types = 1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\ProducerInterface;

/**
 * @see \Jellyfish\QueueRabbitMq\FanoutProducerTest
 */
class FanoutProducer implements ProducerInterface
{
    protected ConnectionInterface $connection;

    protected MessageMapperInterface $messageMapper;

    protected AmqpMessageFactoryInterface $amqpMessageFactory;

    /**
     * @param \Jellyfish\QueueRabbitMq\ConnectionInterface $connection
     * @param \Jellyfish\Queue\MessageMapperInterface $messageMapper
     * @param \Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface $amqpMessageFactory
     */
    public function __construct(
        ConnectionInterface $connection,
        MessageMapperInterface $messageMapper,
        AmqpMessageFactoryInterface $amqpMessageFactory
    ) {
        $this->connection = $connection;
        $this->messageMapper = $messageMapper;
        $this->amqpMessageFactory = $amqpMessageFactory;
    }


    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\ProducerInterface
     */
    public function sendMessage(DestinationInterface $destination, MessageInterface $message): ProducerInterface
    {
        $json = $this->messageMapper->toJson($message);
        $amqpMessage = $this->amqpMessageFactory->create($json);

        $this->connection->createExchange($destination);
        $this->connection->getChannel()->basic_publish($amqpMessage, $destination->getName());

        return $this;
    }
}
