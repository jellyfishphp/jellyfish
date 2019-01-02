<?php

namespace Jellyfish\Event;

interface EventQueueProducerInterface
{
    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventInterface $event
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventQueueProducerInterface
     */
    public function enqueueEvent(
        string $eventName,
        EventInterface $event,
        EventListenerInterface $listener
    ): EventQueueProducerInterface;
}
