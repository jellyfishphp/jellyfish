<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;
use Jellyfish\Queue\MessageInterface;

interface ProducerInterface
{
    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\QueueRabbitMq\ProducerInterface
     */
    public function sendMessage(DestinationInterface $destination, MessageInterface $message): ProducerInterface;
}
