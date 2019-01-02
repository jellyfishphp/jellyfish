<?php

namespace Jellyfish\Event;

interface EventInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setName(string $name): EventInterface;

    /**
     * @return object
     */
    public function getPayload(): object;

    /**
     * @param object $payload
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setPayload(object $payload): EventInterface;
}
