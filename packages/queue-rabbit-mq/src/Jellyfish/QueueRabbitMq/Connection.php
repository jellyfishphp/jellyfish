<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Exception;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\QueueRabbitMq\Exception\MissingDestinationPropertyException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;

use function strtolower;

class Connection implements ConnectionInterface
{
    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel|null
     */
    protected ?AMQPChannel $channel = null;

    /**
     * @var \PhpAmqpLib\Connection\AbstractConnection
     */
    protected AbstractConnection $connection;

    /**
     * @param \PhpAmqpLib\Connection\AbstractConnection $connection
     */
    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        if ($this->channel !== null) {
            return $this->channel;
        }

        $this->channel = $this->connection->channel();

        return $this->channel;
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    public function createExchange(DestinationInterface $destination): ConnectionInterface
    {
        $this->getChannel()->exchange_declare(
            $destination->getName(),
            strtolower($destination->getType()),
            false,
            true,
            false
        );

        return $this;
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    public function createQueue(DestinationInterface $destination): ConnectionInterface
    {
        $this->getChannel()->queue_declare(
            $destination->getName(),
            false,
            true,
            false,
            false
        );

        return $this;
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    public function createQueueAndBind(DestinationInterface $destination): ConnectionInterface
    {
        $this->createQueue($destination);

        $bind = $destination->getProperty('bind');

        if ($bind === null) {
            throw new MissingDestinationPropertyException('Destination property "bind" is not set.');
        }

        $this->getChannel()->queue_bind($destination->getName(), $bind);

        return $this;
    }

    /**
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    public function close(): ConnectionInterface
    {
        if ($this->channel !== null) {
            $this->channel->close();
        }

        if ($this->connection->isConnected()) {
            $this->connection->close();
        }

        return $this;
    }

    public function __destruct()
    {
        try {
            $this->close();
        } catch (Exception $exception) {
            return;
        }
    }
}
