<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;

class FanoutProducer implements ProducerInterface
{
    /**
     * @var \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * @var \Jellyfish\QueueRabbitMq\MessageMapperInterface
     */
    protected MessageMapperInterface $messageMapper;

    /**
     * @var \Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface
     */
    protected AmqpMessageFactoryInterface $amqpMessageFactory;

    /**
     * @param \Jellyfish\QueueRabbitMq\ConnectionInterface $connection
     * @param \Jellyfish\QueueRabbitMq\MessageMapperInterface $messageMapper
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
     * @return \Jellyfish\QueueRabbitMq\ProducerInterface
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
