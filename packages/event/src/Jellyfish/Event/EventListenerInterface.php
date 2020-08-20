<?php

declare(strict_types=1);

namespace Jellyfish\Event;

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
     * @return \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    public function getErrorHandlers(): array;

    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface[] $eventErrorHandlers
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function setErrorHandlers(array $eventErrorHandlers): EventListenerInterface;

    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface $eventErrorHandler
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function addErrorHandler(EventErrorHandlerInterface $eventErrorHandler): EventListenerInterface;

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventListenerInterface
     */
    public function handle(EventInterface $event): EventListenerInterface;
}
