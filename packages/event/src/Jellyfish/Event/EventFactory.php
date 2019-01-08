<?php

namespace Jellyfish\Event;

class EventFactory implements EventFactoryInterface
{
    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function create(): EventInterface
    {
        return new Event();
    }
}
