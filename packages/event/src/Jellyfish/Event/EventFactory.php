<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Queue\QueueFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Jellyfish\Uuid\UuidFacadeInterface;

class EventFactory
{
    /**
     * @var \Jellyfish\Process\ProcessFacadeInterface
     */
    protected ProcessFacadeInterface $processFacade;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface
     */
    protected QueueFacadeInterface $queueFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected SerializerFacadeInterface $serializerFacade;

    /**
     * @var \Jellyfish\Uuid\UuidFacadeInterface
     */
    protected UuidFacadeInterface $uuidFacade;

    /**
     * @var string
     */
    protected string $rootDir;

    /**
     * @var \Jellyfish\Event\EventDispatcherInterface|null
     */
    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * @var \Jellyfish\Event\EventListenerProviderInterface|null
     */
    protected ?EventListenerProviderInterface $eventListenerProvider = null;

    /**
     * @var \Jellyfish\Event\EventQueueConsumerInterface|null
     */
    protected ?EventQueueConsumerInterface $eventQueueConsumer = null;

    /**
     * @var \Jellyfish\Event\EventErrorHandlerProviderInterface|null
     */
    protected ?EventErrorHandlerProviderInterface $defaultEventErrorHandlerProvider = null;

    /**
     * @param \Jellyfish\Process\ProcessFacadeInterface $processFacade
     * @param \Jellyfish\Queue\QueueFacadeInterface $queueFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     * @param \Jellyfish\Uuid\UuidFacadeInterface $uuidFacade
     * @param string $rootDir
     */
    public function __construct(
        ProcessFacadeInterface $processFacade,
        QueueFacadeInterface $queueFacade,
        SerializerFacadeInterface $serializerFacade,
        UuidFacadeInterface $uuidFacade,
        string $rootDir
    ) {
        $this->processFacade = $processFacade;
        $this->queueFacade = $queueFacade;
        $this->serializerFacade = $serializerFacade;
        $this->uuidFacade = $uuidFacade;
        $this->rootDir = $rootDir;
    }

    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function createEvent(): EventInterface
    {
        return new Event($this->uuidFacade->generateUuid());
    }

    /**
     * @return \Jellyfish\Event\EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher(
                $this->getEventListenerProvider(),
                $this->createEventQueueProducer()
            );
        }

        return $this->eventDispatcher;
    }

    /**
     * @return \Jellyfish\Event\EventListenerProviderInterface
     */
    public function getEventListenerProvider(): EventListenerProviderInterface
    {
        if ($this->eventListenerProvider === null) {
            $this->eventListenerProvider = new EventListenerProvider();
        }

        return $this->eventListenerProvider;
    }

    /**
     * @return \Jellyfish\Event\EventQueueProducerInterface
     */
    protected function createEventQueueProducer(): EventQueueProducerInterface
    {
        return new EventQueueProducer(
            $this->createEventMapper(),
            $this->queueFacade
        );
    }

    /**
     * @return \Jellyfish\Event\EventMapperInterface
     */
    protected function createEventMapper(): EventMapperInterface
    {
        return new EventMapper(
            $this,
            $this->queueFacade,
            $this->serializerFacade
        );
    }

    public function createEventQueueWorker(): EventQueueWorkerInterface
    {
        return new EventQueueWorker(
            $this->getEventListenerProvider(),
            $this->getEventQueueConsumer()
        );
    }

    /**
     * @return \Jellyfish\Event\EventQueueConsumerInterface
     */
    public function getEventQueueConsumer(): EventQueueConsumerInterface
    {
        if ($this->eventQueueConsumer === null) {
            $this->eventQueueConsumer = new EventQueueConsumer(
                $this->processFacade,
                $this->createEventMapper(),
                $this->createEventQueueNameGenerator(),
                $this->queueFacade,
                $this->rootDir
            );
        }

        return $this->eventQueueConsumer;
    }

    /**
     * @return \Jellyfish\Event\EventQueueNameGeneratorInterface
     */
    protected function createEventQueueNameGenerator(): EventQueueNameGeneratorInterface
    {
        return new EventQueueNameGenerator();
    }

    /**
     * @return \Jellyfish\Event\EventErrorHandlerProviderInterface
     */
    public function getDefaultEventErrorHandlerProvider(): EventErrorHandlerProviderInterface
    {
        if ($this->defaultEventErrorHandlerProvider === null) {
            $this->defaultEventErrorHandlerProvider = new EventErrorHandlerProvider();
        }

        return $this->defaultEventErrorHandlerProvider;
    }
}
