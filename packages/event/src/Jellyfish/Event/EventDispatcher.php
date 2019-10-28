<?php

declare(strict_types=1);

namespace Jellyfish\Event;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var \Jellyfish\Event\EventListenerProviderInterface
     */
    protected $eventListenerProvider;

    /**
     * @var \Jellyfish\Event\EventQueueProducerInterface
     */
    protected $eventQueueProducer;

    /**
     * @param \Jellyfish\Event\EventListenerProviderInterface $eventListenerProvider
     * @param \Jellyfish\Event\EventQueueProducerInterface $eventQueueProducer
     */
    public function __construct(
        EventListenerProviderInterface $eventListenerProvider,
        EventQueueProducerInterface $eventQueueProducer
    ) {
        $this->eventListenerProvider = $eventListenerProvider;
        $this->eventQueueProducer = $eventQueueProducer;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function dispatch(EventInterface $event): EventDispatcherInterface
    {
        $this->dispatchSync($event);
        $this->dispatchAsync($event);

        return $this;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    protected function dispatchSync(EventInterface $event): EventDispatcherInterface
    {
        $listeners = $this->eventListenerProvider->getListenersByTypeAndEventName(
            EventListenerInterface::TYPE_SYNC,
            $event->getName()
        );

        foreach ($listeners as $listener) {
            /** @var \Jellyfish\Event\EventListenerInterface $listener */
            $listener->handle($event);
        }

        return $this;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    protected function dispatchAsync(EventInterface $event): EventDispatcherInterface
    {
        $listeners = $this->eventListenerProvider->getListenersByTypeAndEventName(
            EventListenerInterface::TYPE_ASYNC,
            $event->getName()
        );

        foreach ($listeners as $listener) {
            $this->eventQueueProducer->enqueueEvent($event, $listener);
        }

        return $this;
    }

    /**
     * @return \Jellyfish\Event\EventListenerProviderInterface
     */
    public function getEventListenerProvider(): EventListenerProviderInterface
    {
        return $this->eventListenerProvider;
    }
}
