<?php

namespace Jellyfish\UuidRamsey;

use Jellyfish\Uuid\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;

class UuidGenerator implements UuidGeneratorInterface
{
    /**
     * @var \Ramsey\Uuid\UuidFactoryInterface
     */
    protected $uuidFactory;

    /**
     * @param \Ramsey\Uuid\UuidFactoryInterface $uuidFactory
     */
    public function __construct(UuidFactoryInterface $uuidFactory)
    {
        $this->uuidFactory = $uuidFactory;
    }


    /**
     * @return string
     */
    public function generate(): string
    {
        return $this->uuidFactory->uuid4()->toString();
    }
}
