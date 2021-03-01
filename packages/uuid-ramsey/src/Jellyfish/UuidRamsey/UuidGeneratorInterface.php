<?php

declare(strict_types=1);

namespace Jellyfish\UuidRamsey;

interface UuidGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
