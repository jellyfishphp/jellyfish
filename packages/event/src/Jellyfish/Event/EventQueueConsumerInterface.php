<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventQueueConsumerInterface
{
    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventQueueConsumerInterface
     */
    public function dequeueAsProcess(string $eventName, string $listenerIdentifier): EventQueueConsumerInterface;

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventInterface|null
     */
    public function dequeue(string $eventName, string $listenerIdentifier): ?EventInterface;

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     * @param int $chunkSize
     *
     * @return \Jellyfish\Event\EventInterface[]
     */
    public function dequeueBulk(string $eventName, string $listenerIdentifier, int $chunkSize): array;
}
