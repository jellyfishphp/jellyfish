<?php

declare(strict_types=1);

namespace Jellyfish\Event;

use Jellyfish\Uuid\UuidFacadeInterface;

class EventFactory implements EventFactoryInterface
{
    /**
     * @var \Jellyfish\Uuid\UuidFacadeInterface
     */
    protected $uuidFacade;

    /**
     * @param \Jellyfish\Uuid\UuidFacadeInterface $uuidFacade
     */
    public function __construct(UuidFacadeInterface $uuidFacade)
    {
        $this->uuidFacade = $uuidFacade;
    }

    /**
     * @return \Jellyfish\Event\EventInterface
     */
    public function create(): EventInterface
    {
        return new Event($this->uuidFacade->generateUuid());
    }
}
