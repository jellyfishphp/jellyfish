<?php

namespace Jellyfish\Queue;

interface ClientInterface
{
    /**
     * @param string $queueName
     *
     * @return string|null
     */
    public function receiveMessage(string $queueName): ?string;

    /**
     * @param string $queueName
     * @param string $message
     *
     * @return \Jellyfish\Queue\ClientInterface
     */
    public function sendMessage(string $queueName, string $message): ClientInterface;
}
