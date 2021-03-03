<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use ArrayObject;
use Jellyfish\Event\Exception\MappingException;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\QueueFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

use function get_class;

class EventMapper implements EventMapperInterface
{
    /**
     * @var \Jellyfish\Event\EventFactory
     */
    protected $eventFactory;

    /**
     * @var \Jellyfish\Queue\QueueFacadeInterface
     */
    protected $queueFacade;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * @param \Jellyfish\Event\EventFactory $eventFactory
     * @param \Jellyfish\Queue\QueueFacadeInterface $queueFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        EventFactory $eventFactory,
        QueueFacadeInterface $queueFacade,
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->eventFactory = $eventFactory;
        $this->queueFacade = $queueFacade;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Event\EventInterface
     *
     * @throws \Jellyfish\Event\Exception\MappingException
     */
    public function fromMessage(MessageInterface $message): EventInterface
    {
        $type = $message->getHeader('body_type');
        $eventName = $message->getHeader('event_name');

        if ($type === null || $eventName === null) {
            throw new MappingException('Could not map message to event.');
        }

        $payload = $this->serializerFacade->deserialize($message->getBody(), $type, 'json');
        $metaProperties = $this->mapHeadersToMetaProperties($message->getHeaders());

        return $this->eventFactory->createEvent()
            ->setName($eventName)
            ->setPayload($payload)
            ->setMetaProperties($metaProperties);
    }

    /**
     * @param \Jellyfish\Event\EventInterface $event
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function toMessage(EventInterface $event): MessageInterface
    {
        $payload = $event->getPayload();
        $metaProperties = $event->getMetaProperties();

        $message = $this->queueFacade->createMessage()
            ->setHeaders($metaProperties)
            ->setHeader('event_name', $event->getName())
            ->setHeader('body_type', get_class($payload))
            ->setBody($this->serializerFacade->serialize($payload, 'json'));

        if (!($payload instanceof ArrayObject)) {
            return $message;
        }

        $type = 'stdClass[]';

        if ($payload->count() !== 0) {
            $type = get_class($payload->offsetGet(0)) . '[]';
        }

        $message->setHeader('body_type', $type);

        return $message;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function mapHeadersToMetaProperties(array $headers): array
    {
        $metaProperties = $headers;

        unset($metaProperties['body_type'], $metaProperties['event_name']);

        return $metaProperties;
    }
}
