<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

interface QueueRabbitMqConstants
{
    public const CONTAINER_KEY_CONNECTION = 'queue_rabbit_mq_connection';
    public const CONTAINER_KEY_AMQP_MESSAGE_FACTORY = 'queue_rabbit_mq_amqp_message_factory';

    public const RABBIT_MQ_HOST = 'RABBIT_MQ_HOST';
    public const DEFAULT_RABBIT_MQ_HOST = '127.0.0.1';

    public const RABBIT_MQ_PORT = 'RABBIT_MQ_PORT';
    public const DEFAULT_RABBIT_MQ_PORT = '5672';

    public const RABBIT_MQ_USER = 'RABBIT_MQ_USER';
    public const DEFAULT_RABBIT_MQ_USER = 'rabbit';

    public const RABBIT_MQ_PASSWORD = 'RABBIT_MQ_PASSWORD';
    public const DEFAULT_RABBIT_MQ_PASSWORD = 'rabbit';

    public const RABBIT_MQ_VHOST = 'RABBIT_MQ_VHOST';
    public const DEFAULT_RABBIT_MQ_VHOST = '/';
}
