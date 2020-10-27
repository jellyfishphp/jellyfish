<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Uuid\UuidGeneratorInterface;

class EventFactory implements EventFactoryInterface
{
    /**
     * @var \Jellyfish\Uuid\UuidGeneratorInterface
     */
    protected $uuidGenerator;

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
