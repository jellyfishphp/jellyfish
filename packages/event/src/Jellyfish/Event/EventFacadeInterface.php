<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventFacadeInterface
{
    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function dispatchEvent(EventInterface $event): EventFacadeInterface;

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $eventListener
     *
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function addEventListener(string $eventName, EventListenerInterface $eventListener): EventFacadeInterface;

    /**
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function startEventQueueWorker(): EventFacadeInterface;

    /**
     * @param string $type
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventListenerInterface|null
     */
    public function getEventListener(
        string $type,
        string $eventName,
        string $listenerIdentifier
    ): ?EventListenerInterface;

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventInterface|null
     */
    public function dequeueEvent(string $eventName, string $listenerIdentifier): ?EventInterface;

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     * @param int $chunkSize
     *
     * @return \Jellyfish\Event\EventInterface[]
     */
    public function dequeueEventBulk(string $eventName, string $listenerIdentifier, int $chunkSize): array;

    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface $eventErrorHandler
     *
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function addDefaultEventErrorHandler(EventErrorHandlerInterface $eventErrorHandler): EventFacadeInterface;

    /**
     * @return \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    public function getDefaultEventErrorHandlers(): array;

    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function createEvent(): EventInterface;
}
