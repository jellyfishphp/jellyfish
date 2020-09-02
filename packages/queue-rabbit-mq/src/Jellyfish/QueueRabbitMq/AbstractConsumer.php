<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\ConsumerInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractConsumer implements ConsumerInterface
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
     * @param \Jellyfish\QueueRabbitMq\ConnectionInterface $connection
     * @param \Jellyfish\Queue\MessageMapperInterface $messageMapper
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
