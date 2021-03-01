<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Throwable;

interface EventErrorHandlerInterface
{
    /**
     * @param string $eventListenerIdentifier
     * @param \Jellyfish\Event\EventInterface $event
     * @param \Throwable $throwable
     *
     * @return \Jellyfish\Event\EventErrorHandlerInterface
     */
    public function handle(
        Throwable $throwable,
        string $eventListenerIdentifier,
        EventInterface $event
    ): EventErrorHandlerInterface;
}
