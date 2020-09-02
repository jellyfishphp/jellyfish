<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use PhpAmqpLib\Channel\AMQPChannel;

interface ConnectionInterface
{
    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    public function getChannel(): AMQPChannel;

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    public function createExchange(DestinationInterface $destination): ConnectionInterface;

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    public function createQueue(DestinationInterface $destination): ConnectionInterface;

    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     *
     * @return \Jellyfish\QueueRabbitMq\ConnectionInterface
     */
    public function createQueueAndBind(DestinationInterface $destination): ConnectionInterface;
}
