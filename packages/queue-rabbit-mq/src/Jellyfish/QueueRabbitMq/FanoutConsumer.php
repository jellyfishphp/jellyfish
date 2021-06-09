<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\QueueRabbitMq\Exception\MissingDestinationPropertyException;

class FanoutConsumer extends AbstractConsumer
{
    /**
     * @var \Jellyfish\QueueRabbitMq\DestinationFactoryInterface
     */
    protected DestinationFactoryInterface $destinationFactory;

    /**
     * @param \Jellyfish\QueueRabbitMq\ConnectionInterface $connection
     * @param \Jellyfish\QueueRabbitMq\MessageMapperInterface $messageMapper
     * @param \Jellyfish\QueueRabbitMq\DestinationFactoryInterface $destinationFactory
     */
    public function __construct(
        ConnectionInterface $connection,
        MessageMapperInterface $messageMapper,
        DestinationFactoryInterface $destinationFactory
    ) {
        parent::__construct($connection, $messageMapper);

        $this->destinationFactory = $destinationFactory;
    }


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
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\QueueRabbitMq\FanoutConsumer
     */
    protected function createExchange(DestinationInterface $destination): FanoutConsumer
    {
        if ($destination->getProperty('bind') === null) {
            throw new MissingDestinationPropertyException('Destination property "bind" is not set.');
        }

        $exchangeDestination = $this->destinationFactory->create()
            ->setName($destination->getProperty('bind'))
            ->setType($destination->getType());

        $this->connection->createExchange($exchangeDestination);

        return $this;
    }
}
