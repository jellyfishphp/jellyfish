<?php

namespace Jellyfish\Event;

class EventQueueWorker implements EventQueueWorkerInterface
{
    /**
     * @var \Jellyfish\Event\EventQueueConsumerInterface
     */
    protected $eventQueueConsumer;
    /**
     * @var \Jellyfish\Event\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param \Jellyfish\Event\EventDispatcherInterface $eventDispatcher
     * @param \Jellyfish\Event\EventQueueConsumerInterface $eventQueueConsumer
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EventQueueConsumerInterface $eventQueueConsumer
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->eventQueueConsumer = $eventQueueConsumer;
    }

    /**
     * @return void
     */
    public function start(): void
    {
        $listeners = $this->eventDispatcher->getListeners(EventListenerInterface::TYPE_ASYNC);

        while (true) {
            foreach ($listeners as $eventName => $listenersPerEvent) {
                foreach ($listenersPerEvent as $listenerIdentifier => $listener) {
                    $this->eventQueueConsumer->dequeueEventAsProcess($eventName, $listenerIdentifier);
                }
            }
        }
    }
}
