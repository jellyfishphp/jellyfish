<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\ConsumerInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\Exception\ConsumerNotFoundException;
use Jellyfish\Queue\Exception\ProducerNotFoundException;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\ProducerInterface;
use Jellyfish\Queue\QueueClientInterface;

use function sprintf;

class QueueClient implements QueueClientInterface
{
    /**
     * @var \Jellyfish\Queue\ConsumerInterface[]
     */
    protected $consumers;

    /**
     * @var \Jellyfish\Queue\ProducerInterface[]
     */
    protected $producers;

    /**
     * @param \Jellyfish\Queue\ConsumerInterface[] $consumers
     * @param \Jellyfish\Queue\ProducerInterface[] $producers
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
     * @param \Jellyfish\Queue\ConsumerInterface $consumer
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    public function setConsumer(string $type, ConsumerInterface $consumer): QueueClientInterface
    {
        $this->consumers[$type] = $consumer;

        return $this;
    }

    /**
     * @param string $type
     * @param \Jellyfish\Queue\ProducerInterface $producer
     *
     * @return \Jellyfish\Queue\QueueClientInterface
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
     * @return \Jellyfish\Queue\QueueClientInterface
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
