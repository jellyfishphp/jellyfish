<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

interface MessageMapperInterface
{
    /**
     * @param string $json
     *
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function fromJson(string $json): MessageInterface;

    /**
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return string
     */
    public function toJson(MessageInterface $message): string;
}
