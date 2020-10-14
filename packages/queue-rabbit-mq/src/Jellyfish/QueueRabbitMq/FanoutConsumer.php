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
        $this->createExchange($destination);
        $this->connection->createQueueAndBind($destination);

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
        $this->createExchange($destination);
        $this->connection->createQueueAndBind($destination);

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
    protected function createExchange(DestinationInterface $destination): void
    {
        $exchange = clone $destination;
        $exchange->setName($destination->getProperty('bind'));
        $this->connection->createExchange($exchange);
    }
}
