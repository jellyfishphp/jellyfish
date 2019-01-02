<?php

namespace Jellyfish\Event;

interface EventQueueWorkerInterface
{
    /**
     * @return void
     */
    public function start(): void;
}
