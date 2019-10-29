<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

interface MessageFactoryInterface
{
    /**
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function create(): MessageInterface;
}
