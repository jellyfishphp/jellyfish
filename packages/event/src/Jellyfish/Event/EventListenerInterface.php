<?php

namespace Jellyfish\Event;

use Closure;

interface EventListenerInterface
{
    public const TYPE_SYNC = 'sync';
    public const TYPE_ASYNC = 'async';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param \Closure|null $errorHandler
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function setErrorHandler(?Closure $errorHandler): EventListenerInterface;

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function handle(EventInterface $event): EventListenerInterface;
}
