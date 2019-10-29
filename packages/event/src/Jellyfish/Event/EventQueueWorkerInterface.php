<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventQueueWorkerInterface
{
    /**
     * @return void
     */
    public function start(): void;
}
