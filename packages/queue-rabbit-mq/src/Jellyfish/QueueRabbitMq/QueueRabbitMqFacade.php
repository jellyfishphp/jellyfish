<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueFacadeInterface;

class QueueRabbitMqFacade implements QueueFacadeInterface
{
    /**
     * @var \Jellyfish\QueueRabbitMq\QueueRabbitMqFactory
     */
    protected QueueRabbitMqFactory $factory;

    /**
     * @param \Jellyfish\QueueRabbitMq\QueueRabbitMqFactory $factory
     */
    public function __construct(QueueRabbitMqFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(DestinationInterface $destination): ?MessageInterface
    {
        return $this->factory->getQueueClient()->receiveMessage($destination);
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param int $limit
     *
     * @return \Jellyfish\Queue\MessageInterface[]
     */
    public function receiveMessages(DestinationInterface $destination, int $limit): array
    {
        return $this->factory->getQueueClient()->receiveMessages($destination, $limit);
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\QueueFacadeInterface
     */
    public function sendMessage(DestinationInterface $destination, MessageInterface $message): QueueFacadeInterface
    {
        $this->factory->getQueueClient()->sendMessage($destination, $message);

        return $this;
    }

    /**
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function createMessage(): MessageInterface
    {
        return $this->factory->createMessage();
    }

    /**
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function createDestination(): DestinationInterface
    {
        return $this->factory->createDestination();
    }
}
