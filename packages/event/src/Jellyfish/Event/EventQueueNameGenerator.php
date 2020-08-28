<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use function sprintf;

class EventQueueNameGenerator implements EventQueueNameGeneratorInterface
{
    /**
     * @param string $eventName
     * @param string $listenerIdentifier
     *
     * @return string
     */
    public function generate(string $eventName, string $listenerIdentifier): string
    {
        return sprintf('%s_%s', $eventName, $listenerIdentifier);
    }
}
