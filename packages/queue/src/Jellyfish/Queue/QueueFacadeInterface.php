<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

interface QueueFacadeInterface
{
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
     * @return \Jellyfish\Queue\QueueFacadeInterface
     */
    public function sendMessage(DestinationInterface $destination, MessageInterface $message): QueueFacadeInterface;

    /**
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function createMessage(): MessageInterface;

    /**
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function createDestination(): DestinationInterface;
}
