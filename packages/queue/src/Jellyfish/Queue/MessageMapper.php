<?php

namespace Jellyfish\Queue;

use Jellyfish\Serializer\SerializerInterface;

class MessageMapper implements MessageMapperInterface
{
    /**
     * @var \Jellyfish\Queue\MessageFactoryInterface
     */
    protected $messageFactory;

    /**
     * @var \Jellyfish\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Jellyfish\Serializer\SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }


    /**
     * @param string $json
     *
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function fromJson(string $json): MessageInterface
    {
        /** @var \Jellyfish\Queue\MessageInterface $message */
        $message = $this->serializer->deserialize($json, Message::class, 'json');

        return $message;
    }

    /**
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return string
     */
    public function toJson(MessageInterface $message): string
    {
        return $this->serializer->serialize($message, 'json');
    }
}
