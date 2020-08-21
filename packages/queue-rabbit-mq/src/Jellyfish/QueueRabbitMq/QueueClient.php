<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\QueueClientInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueClient implements QueueClientInterface
{
    /**
     * @var \PhpAmqpLib\Connection\AbstractConnection
     */
    protected $connection;

    /**
     * @var \Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface
     */
    protected $amqpMessageFactory;

    /**
     * @var \Jellyfish\Queue\MessageMapperInterface
     */
    protected $messageMapper;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel|null
     */
    protected $channel;

    /**
     * @param \PhpAmqpLib\Connection\AbstractConnection $connection
     * @param \Jellyfish\QueueRabbitMq\AmqpMessageFactoryInterface $amqpMessageFactory
     * @param \Jellyfish\Queue\MessageMapperInterface $messageMapper
     */
    public function __construct(
        AbstractConnection $connection,
        AmqpMessageFactoryInterface $amqpMessageFactory,
        MessageMapperInterface $messageMapper
    ) {
        $this->connection = $connection;
        $this->amqpMessageFactory = $amqpMessageFactory;
        $this->messageMapper = $messageMapper;
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(string $queueName): ?MessageInterface
    {
        $this->declareQueue($queueName);

        return $this->doReceiveMessage($queueName);
    }

    protected function doReceiveMessage(string $queueName): ?MessageInterface
    {
        $messageAsJson = $this->getChannel()->basic_get($queueName, true);

        if ($messageAsJson === null || !($messageAsJson instanceof AMQPMessage)) {
            return null;
        }

        return $this->messageMapper->fromJson($messageAsJson->getBody());
    }

    /**
     * @param string $queueName
     * @param int $count
     *
     * @return \Jellyfish\Queue\MessageInterface[]
     */
    public function receiveMessages(string $queueName, int $count): array
    {
        $receivedMessages = [];
        $this->declareQueue($queueName);

        for ($i = 0; $i < $count; $i++) {
            $receivedMessage = $this->doReceiveMessage($queueName);

            if ($receivedMessage === null) {
                return $receivedMessages;
            }

            $receivedMessages[] = $receivedMessage;
        }

        return $receivedMessages;
    }

    /**
     * @param string $queueName
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    public function sendMessage(string $queueName, MessageInterface $message): QueueClientInterface
    {
        $amqpMessageBody = $this->messageMapper->toJson($message);
        $amqpMessage = $this->amqpMessageFactory->create($amqpMessageBody);

        $this->declareQueue($queueName);
        $this->getChannel()->basic_publish($amqpMessage, '', $queueName);

        return $this;
    }
    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    protected function declareQueue(string $queueName): QueueClientInterface
    {
        $this->getChannel()->queue_declare($queueName, false, true);

        return $this;
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    protected function getChannel(): AMQPChannel
    {
        if ($this->channel === null) {
            $this->channel = $this->connection->channel();
        }

        return $this->channel;
    }

    public function __destruct()
    {
        if ($this->connection->isConnected()) {
            $this->connection->close();
        }
    }
}
