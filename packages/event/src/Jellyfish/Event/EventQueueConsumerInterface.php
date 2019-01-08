<?php

namespace Jellyfish\Event;

interface EventQueueConsumerInterface
{
    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     * @return \Jellyfish\Event\EventQueueConsumerInterface
     */
    public function dequeueEventAsProcess(string $eventName, string $listenerIdentifier): EventQueueConsumerInterface;

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     * @return \Jellyfish\Event\EventInterface|null
     */
    public function dequeueEvent(string $eventName, string $listenerIdentifier): ?EventInterface;
}
