<?php

namespace Jellyfish\Event;

interface EventListenerInterface
{
    public const TYPE_SYNC = 'sync';
    public const TYPE_ASYNC = 'async';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param \Jellyfish\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event): void;
}
