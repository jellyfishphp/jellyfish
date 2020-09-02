<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

interface QueueClientInterface
{
    /**
     * @param string $type
     * @param \Jellyfish\Queue\ConsumerInterface $consumer
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    public function setConsumer(string $type, ConsumerInterface $consumer): QueueClientInterface;

    /**
     * @param string $type
     * @param \Jellyfish\Queue\ProducerInterface $producer
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    public function setProducer(string $type, ProducerInterface $producer): QueueClientInterface;

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(DestinationInterface $destination): ?MessageInterface;

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param int $limit
     *
     * @return \Jellyfish\Queue\MessageInterface[]
     */
    public function receiveMessages(DestinationInterface $destination, int $limit): array;

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    public function sendMessage(DestinationInterface $destination, MessageInterface $message): QueueClientInterface;
}
