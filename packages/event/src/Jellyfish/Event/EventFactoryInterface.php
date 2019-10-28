<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventFactoryInterface
{
    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function create(): EventInterface;
}
