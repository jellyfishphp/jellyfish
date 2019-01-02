<?php

namespace Jellyfish\Event;

interface EventQueueNameGeneratorInterface
{
    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return string
     */
    public function generate(string $eventName, string $listenerIdentifier): string;
}
