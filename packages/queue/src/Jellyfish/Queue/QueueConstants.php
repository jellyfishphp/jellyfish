<?php

namespace Jellyfish\Queue;

interface QueueConstants
{
    public const CONTAINER_KEY_MESSAGE_FACTORY = 'message_factory';
    public const CONTAINER_KEY_MESSAGE_MAPPER = 'message_mapper';
    public const CONTAINER_KEY_DESTINATION_FACTORY = 'destination_factory';
    public const CONTAINER_KEY_QUEUE_CLIENT = 'queue_client';
}
