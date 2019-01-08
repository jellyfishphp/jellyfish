<?php

namespace Jellyfish\Event;

interface EventFactoryInterface
{
    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function create(): EventInterface;
}
