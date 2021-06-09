<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Jellyfish\Queue\DestinationInterface;

class Destination implements DestinationInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string[]
     */
    protected array $properties;

    public function __construct()
    {
        $this->properties = [];
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
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function setName(string $name): DestinationInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function setType(string $type): DestinationInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getProperty(string $name): ?string
    {
        if (!isset($this->properties[$name])) {
            return null;
        }

        return $this->properties[$name];
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return \Jellyfish\Queue\DestinationInterface
     */
    public function setProperty(string $name, string $value): DestinationInterface
    {
        $this->properties[$name] = $value;

        return $this;
    }
}
