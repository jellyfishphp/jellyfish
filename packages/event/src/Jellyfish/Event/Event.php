<?php

declare(strict_types=1);

namespace Jellyfish\Event;

class Event implements EventInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var object
     */
    protected $payload;

    /**
     * @var string[]
     */
    protected $metaProperties;

    public function __construct()
    {
        $this->metaProperties = [];
    }

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

    /**
     * @return string[]
     */
    public function getMetaProperties(): array
    {
        return $this->metaProperties;
    }

    /**
     * @param string[] $metaProperties
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setMetaProperties(array $metaProperties): EventInterface
    {
        $this->metaProperties = $metaProperties;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getMetaProperty(string $key): ?string
    {
        if (!\array_key_exists($key, $this->metaProperties)) {
            return null;
        }
        return $this->metaProperties[$key];
    }

    /**
     * @param string $key
     * @param string $metaProperty
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setMetaProperty(string $key, string $metaProperty): EventInterface
    {
        $this->metaProperties[$key] = $metaProperty;

        return $this;
    }
}
