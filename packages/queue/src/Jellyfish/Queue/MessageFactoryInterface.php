<?php

namespace Jellyfish\Queue;

interface MessageFactoryInterface
{
    /**
     * @return \Jellyfish\Queue\MessageInterface
     */
    public function create(): MessageInterface;
}
