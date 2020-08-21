<?php

namespace Jellyfish\QueueRabbitMq;

use PhpAmqpLib\Message\AMQPMessage;

interface AmqpMessageFactoryInterface
{
    /**
     * @param string $body
     *
     * @return \PhpAmqpLib\Message\AMQPMessage
     */
    public function create(string $body): AMQPMessage;
}
