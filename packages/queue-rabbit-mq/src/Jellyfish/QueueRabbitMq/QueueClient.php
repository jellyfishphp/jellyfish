<?php

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\QueueClientInterface;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueClient implements QueueClientInterface
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
     * @var \Jellyfish\Queue\MessageMapperInterface
     */
    protected $messageMapper;

    /**
     * @param \PhpAmqpLib\Connection\AbstractConnection $connection
     * @param \Jellyfish\Queue\MessageMapperInterface $messageMapper
     */
    public function __construct(AbstractConnection $connection, MessageMapperInterface $messageMapper)
    {
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
        $this->messageMapper = $messageMapper;
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(string $queueName): ?MessageInterface
    {
        $this->channel->queue_declare($queueName);

        $messageAsJson = $this->channel->basic_get($queueName, true);
        
        if ($messageAsJson === null || !($messageAsJson instanceof AMQPMessage)) {
            return null;
        }

        return $this->messageMapper->fromJson($messageAsJson->getBody());
    }

    /**
     * @param string $queueName
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    public function sendMessage(string $queueName, MessageInterface $message): QueueClientInterface
    {
        $messageAsJson = $this->messageMapper->toJson($message);

        $this->channel->queue_declare($queueName);
        $this->channel->basic_publish(new AMQPMessage($messageAsJson), '', $queueName);

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
