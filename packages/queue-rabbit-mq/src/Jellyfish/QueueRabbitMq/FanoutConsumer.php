<?php

declare(strict_types = 1);

namespace Jellyfish\QueueRabbitMq;

use Exception;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;

/**
 * @see \Jellyfish\QueueRabbitMq\FanoutConsumerTest
 */
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

        for ($i = 0; $i < $limit; ++$i) {
            $receivedMessage = $this->doReceiveMessage($destination);

            if (!$receivedMessage instanceof MessageInterface) {
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
        $bindProperty = $destination->getProperty('bind');

        if ($bindProperty === null) {
            throw new Exception('Property "bind" is required for fanout exchange.');
        }

        $exchange->setName($bindProperty);
        $this->connection->createExchange($exchange);
    }
}
