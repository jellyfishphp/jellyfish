<?php

declare(strict_types=1);

namespace Jellyfish\Event;

class EventFacade implements EventFacadeInterface
{
    /**
     * @var \Jellyfish\Event\EventFactory
     */
    protected EventFactory $factory;

    /**
     * @param \Jellyfish\Event\EventFactory $factory
     */
    public function __construct(EventFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function dispatchEvent(EventInterface $event): EventFacadeInterface
    {
        $this->factory->getEventDispatcher()->dispatch($event);

        return $this;
    }

    /**
     * @param string $eventName
     * @param \Jellyfish\Event\EventListenerInterface $eventListener
     *
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function addEventListener(string $eventName, EventListenerInterface $eventListener): EventFacadeInterface
    {
        $this->factory->getEventListenerProvider()->addListener($eventName, $eventListener);

        return $this;
    }

    /**
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function startEventQueueWorker(): EventFacadeInterface
    {
        $this->factory->createEventQueueWorker()->start();

        return $this;
    }

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
    ): ?EventListenerInterface {
        return $this->factory->getEventListenerProvider()->getListener($type, $eventName, $listenerIdentifier);
    }

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return \Jellyfish\Event\EventInterface|null
     */
    public function dequeueEvent(string $eventName, string $listenerIdentifier): ?EventInterface
    {
        return $this->factory->getEventQueueConsumer()->dequeue($eventName, $listenerIdentifier);
    }

    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     * @param int $chunkSize
     *
     * @return \Jellyfish\Event\EventInterface[]
     */
    public function dequeueEventBulk(string $eventName, string $listenerIdentifier, int $chunkSize): array
    {
        return $this->factory->getEventQueueConsumer()->dequeueBulk($eventName, $listenerIdentifier, $chunkSize);
    }

    /**
     * @param \Jellyfish\Event\EventErrorHandlerInterface $eventErrorHandler
     *
     * @return \Jellyfish\Event\EventFacadeInterface
     */
    public function addDefaultEventErrorHandler(EventErrorHandlerInterface $eventErrorHandler): EventFacadeInterface
    {
        $this->factory->getDefaultEventErrorHandlerProvider()->add($eventErrorHandler);

        return $this;
    }

    /**
     * @return \Jellyfish\Event\EventErrorHandlerInterface[]
     */
    public function getDefaultEventErrorHandlers(): array
    {
        return $this->factory->getDefaultEventErrorHandlerProvider()->getAll();
    }

    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function createEvent(): EventInterface
    {
        return $this->factory->createEvent();
    }
}
