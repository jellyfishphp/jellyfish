<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Queue\QueueClientInterface;

class EventQueueProducer implements EventQueueProducerInterface
{
    /**
     * @var \Jellyfish\Event\EventMapperInterface
     */
    protected $eventMapper;

    /**
     * @var \Jellyfish\Queue\QueueClientInterface
     */
    protected $queueClient;

    /**
     * @var \Jellyfish\Event\EventQueueNameGeneratorInterface
     */
    protected $eventQueueNameGenerator;

    /**
     * @param \Jellyfish\Event\EventMapperInterface $eventMapper
     * @param \Jellyfish\Event\EventQueueNameGeneratorInterface $eventQueueNameGenerator
     * @param \Jellyfish\Queue\QueueClientInterface $queueClient
     */
    public function __construct(
        EventMapperInterface $eventMapper,
        EventQueueNameGeneratorInterface $eventQueueNameGenerator,
        QueueClientInterface $queueClient
    ) {
        $this->eventMapper = $eventMapper;
        $this->eventQueueNameGenerator = $eventQueueNameGenerator;
        $this->queueClient = $queueClient;
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     * @param \Jellyfish\Event\EventListenerInterface $listener
     *
     * @return \Jellyfish\Event\EventQueueProducerInterface
     */
    public function enqueue(
        EventInterface $event,
        EventListenerInterface $listener
    ): EventQueueProducerInterface {
        $eventQueueName = $this->eventQueueNameGenerator->generate($event->getName(), $listener->getIdentifier());
        $message = $this->eventMapper->toMessage($event);

        $this->queueClient->sendMessage($eventQueueName, $message);

        return $this;
    }
}
