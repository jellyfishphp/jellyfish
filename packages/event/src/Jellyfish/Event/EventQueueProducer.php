<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\QueueFacadeInterface;

class EventQueueProducer implements EventQueueProducerInterface
{
    /**
     * @var \Jellyfish\Event\EventMapperInterface
     */
    protected $eventMapper;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface
     */
    protected $queueFacade;

    /**
     * @param \Jellyfish\Event\EventMapperInterface $eventMapper
     * @param \Jellyfish\Queue\QueueFacadeInterface $queueFacade
     */
    public function __construct(
        EventMapperInterface $eventMapper,
        QueueFacadeInterface $queueFacade
    ) {
        $this->eventMapper = $eventMapper;
        $this->queueFacade = $queueFacade;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return \Jellyfish\Event\EventQueueProducerInterface
     */
    public function enqueue(
        EventInterface $event
    ): EventQueueProducerInterface {
        $destination = $this->queueFacade->createDestination()
            ->setName($event->getName())
            ->setType(DestinationInterface::TYPE_FANOUT);

        $message = $this->eventMapper->toMessage($event);

        $this->queueFacade->sendMessage($destination, $message);

        return $this;
    }
}
