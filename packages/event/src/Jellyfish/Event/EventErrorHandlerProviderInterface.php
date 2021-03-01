<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventErrorHandlerProviderInterface
{
    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface $eventErrorHandler
     * @return \Jellyfish\Event\EventErrorHandlerProviderInterface
     */
    public function add(EventErrorHandlerInterface $eventErrorHandler): EventErrorHandlerProviderInterface;

    /**
     * @return \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    public function getAll(): array;
}
