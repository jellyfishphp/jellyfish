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
     * @var \PhpAmqpLib\Channel\AMQPChannel|null
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
        $this->messageMapper = $messageMapper;
    }

    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(string $queueName): ?MessageInterface
    {
        $this->getChannel()->queue_declare($queueName);

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
        $messageMapper = $this->messageMapper;

        $this->getChannel()->queue_declare($queueName);
        $this->getChannel()->basic_qos(0, $count, false);
        $this->getChannel()->basic_consume(
            $queueName,
            '',
            false,
            false,
            false,
            false,
            static function (AMQPMessage $message) use (&$receivedMessages, $messageMapper): void {
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
                $receivedMessages[] = $messageMapper->fromJson($message->getBody());
            }
        );

        while ($this->getChannel()->is_consuming()) {
            $this->getChannel()->wait(null, false, 10);
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
        $messageAsJson = $this->messageMapper->toJson($message);

        $this->getChannel()->queue_declare($queueName);
        $this->getChannel()->basic_publish(new AMQPMessage($messageAsJson), '', $queueName);

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
