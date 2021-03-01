<?php

declare(strict_types=1);

namespace Jellyfish\Event;

class EventErrorHandlerProvider implements EventErrorHandlerProviderInterface
{
    /**
     * @var \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    protected $eventErrorHandlers;

    public function __construct()
    {
        $this->eventErrorHandlers = [];
    }

    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface $eventErrorHandler
     *
     * @return \Jellyfish\Event\EventErrorHandlerProviderInterface
     */
    public function add(EventErrorHandlerInterface $eventErrorHandler): EventErrorHandlerProviderInterface
    {
        $this->eventErrorHandlers[] = $eventErrorHandler;

        return $this;
    }

    /**
     * @return \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    public function getAll(): array
    {
        return $this->eventErrorHandlers;
    }
}
