<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

/**
 * @see \Jellyfish\Queue\MessageFactoryTest
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function create(): MessageInterface
    {
        return new Message();
    }
}
