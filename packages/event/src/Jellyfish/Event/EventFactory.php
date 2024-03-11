<?php

declare(strict_types = 1);

namespace Jellyfish\Event;

use Jellyfish\Uuid\UuidGeneratorInterface;

/**
 * @see \Jellyfish\Event\EventFactoryTest
 */
class EventFactory implements EventFactoryInterface
{
    protected UuidGeneratorInterface $uuidGenerator;

    /**
     * @param \Jellyfish\Uuid\UuidGeneratorInterface $uuidGenerator
     */
    public function __construct(UuidGeneratorInterface $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function create(): EventInterface
    {
        return new Event($this->uuidGenerator->generate());
    }
}
