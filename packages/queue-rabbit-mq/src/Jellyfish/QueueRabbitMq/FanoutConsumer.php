<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;

class FanoutConsumer extends AbstractConsumer
{
    /**
     * @param  \Jellyfish\Queue\DestinationInterface  $destination
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(DestinationInterface $destination): ?MessageInterface
    {
        $this->createQueueAndBind($destination);

        return $this->doReceiveMessage($destination);
    }

    /**
     * @param  \Jellyfish\Queue\DestinationInterface  $destination
     * @param  int  $limit
     *
     * @return \Jellyfish\Queue\MessageInterface[]
     */
    public function receiveMessages(DestinationInterface $destination, int $limit): array
    {
        $receivedMessages = [];
        $this->createQueueAndBind($destination);

        for ($i = 0; $i < $limit; $i++) {
            $receivedMessage = $this->doReceiveMessage($destination);

            if ($receivedMessage === null) {
                return $receivedMessages;
            }

            $receivedMessages[] = $receivedMessage;
        }

        return $receivedMessages;
    }

    /**
     * @param  \Jellyfish\Queue\DestinationInterface  $destination
     *
     * @return void
     */
    protected function createQueueAndBind(DestinationInterface $destination): void
    {
        try {
            $backupConnection = clone $this->connection;
            $this->connection->createQueueAndBind($destination);
            $backupConnection->getChannel()->close();
        } catch (\Exception $exception) {
            if ($exception->getCode() === 404) {
                $this->connection = $backupConnection;
                $exchange = clone $destination;
                $exchange->setName($destination->getProperty('bind'));
                $this->connection->createExchange($exchange);
                $this->connection->createQueueAndBind($destination);
            }
        }
    }
}
