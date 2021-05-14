<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractConsumer implements ConsumerInterface
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
     * @param \Jellyfish\QueueRabbitMq\ConnectionInterface $connection
     * @param \Jellyfish\QueueRabbitMq\MessageMapperInterface $messageMapper
     */
    public function __construct(
        ConnectionInterface $connection,
        MessageMapperInterface $messageMapper
    ) {
        $this->connection = $connection;
        $this->messageMapper = $messageMapper;
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    protected function doReceiveMessage(DestinationInterface $destination): ?MessageInterface
    {
        $messageAsJson = $this->connection->getChannel()->basic_get($destination->getName(), true);

        if ($messageAsJson === null || !($messageAsJson instanceof AMQPMessage)) {
            return null;
        }

        return $this->messageMapper->fromJson($messageAsJson->getBody());
    }
}
