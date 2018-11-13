<?php

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\ClientInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Client implements ClientInterface
{
    /**
     * @var \PhpAmqpLib\Connection\AbstractConnection
     */
    protected $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $channel;

    /**
     * @param \PhpAmqpLib\Connection\AbstractConnection $connection
     */
    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
    }

    /**
     * @param string $queueName
     *
     * @return string|null
     */
    public function receiveMessage(string $queueName): ?string
    {
        $this->channel->queue_declare($queueName);

        return $this->channel->basic_get($queueName);
    }

    /**
     * @param string $queueName
     * @param string $message
     *
     * @return \Jellyfish\Queue\ClientInterface
     */
    public function sendMessage(string $queueName, string $message): ClientInterface
    {
        $this->channel->queue_declare($queueName);
        $this->channel->basic_publish(new AMQPMessage($message), '', $queueName);

        return $this;
    }

    public function __destruct()
    {
        if ($this->channel !== null) {
            $this->channel->close();
        }

        $this->connection->close();
    }
}
