<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\ProducerInterface;

class QueueProducer implements ProducerInterface
{
    /**
     * @var \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \Jellyfish\Queue\MessageMapperInterface
     */
    protected $messageMapper;

    /**
     * @var \Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface
     */
    protected $amqpMessageFactory;

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

        $this->connection->createQueue($destination);
        $this->connection->getChannel()->basic_publish($amqpMessage, '', $destination->getName());

        return $this;
    }
}
