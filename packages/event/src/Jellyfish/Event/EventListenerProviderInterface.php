<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventListenerProviderInterface
{
    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventListenerProviderInterface
     */
    public function addListener(string $eventName, EventListenerInterface $listener): EventListenerProviderInterface;

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventListenerProviderInterface
     */
    public function removeListener(string $eventName, EventListenerInterface $listener): EventListenerProviderInterface;

    /**
     * @return array
     */
    public function getAllListeners(): array;

    /**
     * @param string $type
     *
     * @return array
     */
    public function getListenersByType(string $type): array;

    /**
     * @param string $type
     * @param string $eventName
     *
     * @return array
     */
    public function getListenersByTypeAndEventName(string $type, string $eventName): array;

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
