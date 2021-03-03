<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Event\Exception\NotSupportedTypeException;

use function array_key_exists;
use function sprintf;

class EventListenerProvider implements EventListenerProviderInterface
{
    /**
     * @var array
     */
    protected $listeners = [
        EventListenerInterface::TYPE_SYNC => [],
        EventListenerInterface::TYPE_ASYNC => []
    ];

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventListenerProviderInterface
     */
    public function addListener(string $eventName, EventListenerInterface $listener): EventListenerProviderInterface
    {
        $type = $listener->getType();

        if (!array_key_exists($eventName, $this->listeners[$type])) {
            $this->listeners[$type][$eventName] = [];
        }

        $this->listeners[$type][$eventName][$listener->getIdentifier()] = $listener;

        return $this;
    }

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventListenerProviderInterface
     */
    public function removeListener(string $eventName, EventListenerInterface $listener): EventListenerProviderInterface
    {
        $type = $listener->getType();
        $listenerIdentifier = $listener->getIdentifier();

        if (!$this->hasListener($type, $eventName, $listenerIdentifier)) {
            return $this;
        }

        unset($this->listeners[$type][$eventName][$listenerIdentifier]);

        return $this;
    }

    /**
     * @param string $type
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventListenerInterface|null
     */
    public function getListener(string $type, string $eventName, string $listenerIdentifier): ?EventListenerInterface
    {
        if (!$this->hasListener($type, $eventName, $listenerIdentifier)) {
            return null;
        }

        return $this->listeners[$type][$eventName][$listenerIdentifier];
    }

    /**
     * @param string $type
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return bool
     */
    public function hasListener(string $type, string $eventName, string $listenerIdentifier): bool
    {
        return array_key_exists($eventName, $this->listeners[$type])
            && array_key_exists($listenerIdentifier, $this->listeners[$type][$eventName]);
    }

    /**
     * @return array
     */
    public function getAllListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @param string $type
     *
     * @return array
     *
     * @throws \Jellyfish\Event\Exception\NotSupportedTypeException
     */
    public function getListenersByType(string $type): array
    {
        if (!array_key_exists($type, $this->listeners)) {
            throw new NotSupportedTypeException(sprintf('Given type "%s" is not supported', $type));
        }

        return $this->listeners[$type];
    }

    /**
     * @param string $type
     * @param string $eventName
     *
     * @return array
     *
     * @throws \Jellyfish\Event\Exception\NotSupportedTypeException
     */
    public function getListenersByTypeAndEventName(string $type, string $eventName): array
    {
        $listeners = $this->getListenersByType($type);

        if (array_key_exists($eventName, $listeners)) {
            return $listeners[$eventName];
        }

        return [];
    }
}
