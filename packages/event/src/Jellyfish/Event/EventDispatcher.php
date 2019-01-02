<?php

namespace Jellyfish\Event;

use Jellyfish\Event\Exception\NotSupportedTypeException;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \Jellyfish\Event\EventQueueProducerInterface
     */
    protected $eventQueueProducer;

    /**
     * @var array
     */
    protected $listeners = [
        EventListenerInterface::TYPE_SYNC => [],
        EventListenerInterface::TYPE_ASYNC => []
    ];


    /**
     * @param \Jellyfish\Event\EventQueueProducerInterface $eventQueueProducer
     */
    public function __construct(
        EventQueueProducerInterface $eventQueueProducer
    ) {
        $this->eventQueueProducer = $eventQueueProducer;
    }

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function addListener(string $eventName, EventListenerInterface $listener): EventDispatcherInterface
    {
        $type = $listener->getType();

        if (!\array_key_exists($eventName, $this->listeners[$type])) {
            $this->listeners[$type][$eventName] = [];
        }

        $this->listeners[$type][$eventName][$listener->getIdentifier()] = $listener;

        return $this;
    }

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function removeListener(string $eventName, EventListenerInterface $listener): EventDispatcherInterface
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
     * @param string $eventName
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function dispatch(string $eventName, EventInterface $event): EventDispatcherInterface
    {
        $this->dispatchSync($eventName, $event);
        $this->dispatchAsync($eventName, $event);

        return $this;
    }

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    protected function dispatchSync(string $eventName, EventInterface $event): EventDispatcherInterface
    {
        $type = EventListenerInterface::TYPE_SYNC;

        if (!\array_key_exists($eventName, $this->listeners[$type])) {
            return $this;
        }

        foreach ($this->listeners[$type][$eventName] as $listener) {
            /** @var \Jellyfish\Event\EventListenerInterface $listener */
            $listener->handle($event);
        }

        return $this;
    }

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    protected function dispatchAsync(string $eventName, EventInterface $event): EventDispatcherInterface
    {
        $type = EventListenerInterface::TYPE_ASYNC;

        if (!\array_key_exists($eventName, $this->listeners[$type])) {
            return $this;
        }

        foreach ($this->listeners[$type][$eventName] as $listener) {
            $this->eventQueueProducer->enqueueEvent($eventName, $event, $listener);
        }

        return $this;
    }

    /**
     * @param string|null $type
     *
     * @return array
     *
     * @throws \Jellyfish\Event\Exception\NotSupportedTypeException
     */
    public function getListeners(string $type = null): array
    {
        if ($type === null) {
            return $this->listeners;
        }

        if (!\array_key_exists($type, $this->listeners)) {
            throw new NotSupportedTypeException(\sprintf('Given type "%s" is not supported', $type));
        }

        return $this->listeners[$type];
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
        return \array_key_exists($eventName, $this->listeners[$type])
            && \array_key_exists($listenerIdentifier, $this->listeners[$type][$eventName]);
    }
}
