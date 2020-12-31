<?php

declare(strict_types=1);

namespace Jellyfish\UuidRamsey;

use Ramsey\Uuid\UuidFactory;

class UuidRamseyFactory
{
    /**
     * @return \Jellyfish\UuidRamsey\UuidGeneratorInterface
     */
    public function createUuidGenerator(): UuidGeneratorInterface
    {
        return new UuidGenerator(new UuidFactory());
    }
}
