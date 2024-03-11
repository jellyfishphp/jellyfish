<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Jellyfish\Queue\DestinationFactoryInterface;
use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\QueueClientInterface;

/**
 * @see \Jellyfish\Event\EventQueueProducerTest
 */
class EventQueueProducer implements EventQueueProducerInterface
{
    protected EventMapperInterface $eventMapper;

    protected QueueClientInterface $queueClient;

    protected DestinationFactoryInterface $destinationFactory;

    /**
     * @param \Jellyfish\Event\EventMapperInterface $eventMapper
     * @param \Jellyfish\Queue\QueueClientInterface $queueClient
     * @param \Jellyfish\Queue\DestinationFactoryInterface $destinationFactory
     */
    public function __construct(
        EventMapperInterface $eventMapper,
        QueueClientInterface $queueClient,
        DestinationFactoryInterface $destinationFactory
    ) {
        $this->eventMapper = $eventMapper;
        $this->queueClient = $queueClient;
        $this->destinationFactory = $destinationFactory;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventQueueProducerInterface
     */
    public function enqueue(
        EventInterface $event
    ): EventQueueProducerInterface {
        $destination = $this->destinationFactory->create()
            ->setName($event->getName())
            ->setType(DestinationInterface::TYPE_FANOUT);

        $message = $this->eventMapper->toMessage($event);

        $this->queueClient->sendMessage($destination, $message);

        return $this;
    }
}
