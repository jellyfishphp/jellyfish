<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\MessageInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;

class MessageMapper implements MessageMapperInterface
{
    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected $serializerFacade;

    /**
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        SerializerFacadeInterface $serializerFacade
    ) {
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @param string $json
     *
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function fromJson(string $json): MessageInterface
    {
        /** @var \Jellyfish\Queue\MessageInterface $message */
        $message = $this->serializerFacade->deserialize($json, Message::class, 'json');

        return $message;
    }

    /**
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return string
     */
    public function toJson(MessageInterface $message): string
    {
        return $this->serializerFacade->serialize($message, 'json');
    }
}
