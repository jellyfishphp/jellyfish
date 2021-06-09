<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\Exception\ConsumerNotFoundException;
use Jellyfish\Queue\Exception\ProducerNotFoundException;
use Jellyfish\Queue\MessageInterface;

use function sprintf;

class QueueClient implements QueueClientInterface
{
    /**
     * @var \Jellyfish\QueueRabbitMq\ConsumerInterface[]
     */
    protected array $consumers;

    /**
     * @var \Jellyfish\QueueRabbitMq\ProducerInterface[]
     */
    protected array $producers;

    /**
     * @param \Jellyfish\QueueRabbitMq\ConsumerInterface[] $consumers
     * @param \Jellyfish\QueueRabbitMq\ProducerInterface[] $producers
     */
    public function __construct(
        array $consumers = [],
        array $producers = []
    ) {
        $this->consumers = $consumers;
        $this->producers = $producers;
    }

    /**
     * @param string $type
     * @param \Jellyfish\QueueRabbitMq\ConsumerInterface $consumer
     *
     * @return \Jellyfish\QueueRabbitMq\QueueClientInterface
     */
    public function setConsumer(string $type, ConsumerInterface $consumer): QueueClientInterface
    {
        $this->consumers[$type] = $consumer;

        return $this;
    }

    /**
     * @param string $type
     * @param \Jellyfish\QueueRabbitMq\ProducerInterface $producer
     *
     * @return \Jellyfish\QueueRabbitMq\QueueClientInterface
     */
    public function setProducer(string $type, ProducerInterface $producer): QueueClientInterface
    {
        $this->producers[$type] = $producer;

        return $this;
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(DestinationInterface $destination): ?MessageInterface
    {
        if (!isset($this->consumers[$destination->getType()])) {
            throw new ConsumerNotFoundException(sprintf(
                'There is no consumer for type "%s".',
                $destination->getType()
            ));
        }

        return $this->consumers[$destination->getType()]->receiveMessage($destination);
    }


    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param int $limit
     *
     * @return \Jellyfish\Queue\MessageInterface[]
     */
    public function receiveMessages(DestinationInterface $destination, int $limit): array
    {
        if (!isset($this->consumers[$destination->getType()])) {
            throw new ConsumerNotFoundException(sprintf(
                'There is no consumer for type "%s".',
                $destination->getType()
            ));
        }

        return $this->consumers[$destination->getType()]->receiveMessages($destination, $limit);
    }

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\QueueRabbitMq\QueueClientInterface
     */
    public function sendMessage(DestinationInterface $destination, MessageInterface $message): QueueClientInterface
    {
        if (!isset($this->producers[$destination->getType()])) {
            throw new ProducerNotFoundException(sprintf(
                'There is no producer for type "%s".',
                $destination->getType()
            ));
        }

        $this->producers[$destination->getType()]->sendMessage($destination, $message);

        return $this;
    }
}
