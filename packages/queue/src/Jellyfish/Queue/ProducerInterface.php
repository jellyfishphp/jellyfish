<?php

declare(strict_types = 1);

namespace Jellyfish\Queue;

interface ProducerInterface
{
    /**
     * @param \Jellyfish\Queue\DestinationInterface $destination
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\ProducerInterface
     */
    public function sendMessage(DestinationInterface $destination, MessageInterface $message): ProducerInterface;
}
