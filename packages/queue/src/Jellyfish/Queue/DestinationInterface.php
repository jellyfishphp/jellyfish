<?php

declare(strict_types=1);

namespace Jellyfish\Queue;

interface DestinationInterface
{
    public const TYPE_QUEUE = 'QUEUE';
    public const TYPE_FANOUT = 'FANOUT';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function setName(string $name): DestinationInterface;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     *
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function setType(string $type): DestinationInterface;

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getProperty(string $name): ?string;

    /**
     * @param string $name
     * @param string $value
     *
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function setProperty(string $name, string $value): DestinationInterface;
}
