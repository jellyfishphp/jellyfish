<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

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
