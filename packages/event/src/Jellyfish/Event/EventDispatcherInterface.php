<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

interface EventDispatcherInterface
{
    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function dispatch(EventInterface $event): EventDispatcherInterface;
}
