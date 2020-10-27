<?php

declare(strict_types=1);

namespace Jellyfish\Event;

interface EventInterface
{
    /**
     * @return string
     */
    public function getId(): string;

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

    /**
     * @return string[]
     */
    public function getMetaProperties(): array;

    /**
     * @param string[] $metaProperties
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setMetaProperties(array $metaProperties): EventInterface;

    /**
     * @param string $key
     *
     * @return string
     */
    public function getMetaProperty(string $key): ?string;

    /**
     * @param string $key
     * @param string $metaProperty
     *
     * @return \Jellyfish\Event\EventInterface
     */
    public function setMetaProperty(string $key, string $metaProperty): EventInterface;
}
