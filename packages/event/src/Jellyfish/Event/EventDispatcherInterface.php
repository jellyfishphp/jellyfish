<?php

namespace Jellyfish\Event;

interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function addListener(string $eventName, EventListenerInterface $listener): EventDispatcherInterface;

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function removeListener(string $eventName, EventListenerInterface $listener): EventDispatcherInterface;

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function dispatch(EventInterface $event): EventDispatcherInterface;

    /**
     * @param string|null $type
     *
     * @return array
     */
    public function getListeners(string $type = null): array;

    /**
     * @param string $type
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return bool
     */
    public function hasListener(string $type, string $eventName, string $listenerIdentifier): bool;

    /**
     * @param string $type
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventListenerInterface|null
     */
    public function getListener(string $type, string $eventName, string $listenerIdentifier): ?EventListenerInterface;
}
