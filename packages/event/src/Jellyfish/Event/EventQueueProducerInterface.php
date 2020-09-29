<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventQueueProducerInterface
{
    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventQueueProducerInterface
     */
    public function enqueue(
        EventInterface $event
    ): EventQueueProducerInterface;
}
