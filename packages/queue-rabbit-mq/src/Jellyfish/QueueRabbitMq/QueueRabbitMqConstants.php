<?php

namespace Jellyfish\QueueRabbitMq;

interface QueueRabbitMqConstants
{
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
