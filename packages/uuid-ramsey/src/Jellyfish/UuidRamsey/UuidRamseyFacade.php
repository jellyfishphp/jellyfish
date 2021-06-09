<?php

declare(strict_types=1);

namespace Jellyfish\UuidRamsey;

use Jellyfish\Uuid\UuidFacadeInterface;

class UuidRamseyFacade implements UuidFacadeInterface
{
    /**
     * @var \Jellyfish\UuidRamsey\UuidRamseyFactory
     */
    protected UuidRamseyFactory $factory;

    /**
     * @param \Jellyfish\UuidRamsey\UuidRamseyFactory $factory
     */
    public function __construct(UuidRamseyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return string
     */
    public function generateUuid(): string
    {
        return $this->factory->createUuidGenerator()->generate();
    }
}
