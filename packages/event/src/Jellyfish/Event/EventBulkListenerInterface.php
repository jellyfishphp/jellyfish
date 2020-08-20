<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventBulkListenerInterface extends EventListenerInterface
{
    /**
     * @return int
     */
    public function getChunkSize(): int;

    /**
     * @param \Jellyfish\Event\EventInterface[] $events
     *
     * @return \Jellyfish\Event\EventBulkListenerInterface
     */
    public function handleBulk(array $events): EventBulkListenerInterface;
}
