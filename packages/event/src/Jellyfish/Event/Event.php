<?php

namespace Jellyfish\Event;

class Event implements EventInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $retries;

    /**
     * @var object
     */
    protected $payload;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setName(string $name): EventInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * @param int $retries
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setRetries(int $retries): EventInterface
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * @return object
     */
    public function getPayload(): object
    {
        return $this->payload;
    }

    /**
     * @param object $payload
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setPayload(object $payload): EventInterface
    {
        $this->payload = $payload;

        return $this;
    }
}
