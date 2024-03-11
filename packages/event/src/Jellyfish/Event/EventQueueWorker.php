<?php

declare(strict_types = 1);

namespace Jellyfish\Event;


class EventQueueWorker implements EventQueueWorkerInterface
{
    protected const DELAY_INTERVAL = 1000000;

    protected EventQueueConsumerInterface $eventQueueConsumer;

    protected EventListenerProviderInterface $eventListenerProvider;

    /**
     * @param \Jellyfish\Event\EventListenerProviderInterface $eventListenerProvider
     * @param \Jellyfish\Event\EventQueueConsumerInterface $eventQueueConsumer
     */
    public function __construct(
        EventListenerProviderInterface $eventListenerProvider,
        EventQueueConsumerInterface $eventQueueConsumer
    ) {
        $this->eventListenerProvider = $eventListenerProvider;
        $this->eventQueueConsumer = $eventQueueConsumer;
    }

    /**
     * @return void
     */
    public function start(): void
    {
        $listeners = $this->eventListenerProvider->getListenersByType(EventListenerInterface::TYPE_ASYNC);

        if (\count($listeners) === 0) {
            return;
        }

        /** @phpstan-ignore-next-line */
        while (true) {
            foreach ($listeners as $eventName => $listenersPerEvent) {
                foreach ($listenersPerEvent as $listenerIdentifier => $listener) {
                    $this->eventQueueConsumer->dequeueAsProcess((string)$eventName, $listenerIdentifier);
                    \usleep(static::DELAY_INTERVAL);
                }
            }
        }
    }
}
