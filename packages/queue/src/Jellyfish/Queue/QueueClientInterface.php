<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

interface QueueClientInterface
{
    /**
     * @param string $queueName
     *
     * @return \Jellyfish\Queue\MessageInterface|null
     */
    public function receiveMessage(string $queueName): ?MessageInterface;

    /**
     * @param string $queueName
     * @param int $count
     *
     * @return \Jellyfish\Queue\MessageInterface[]
     */
    public function receiveMessages(string $queueName, int $count): array;

    /**
     * @param string $queueName
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     */
    public function sendMessage(string $queueName, MessageInterface $message): QueueClientInterface;
}
