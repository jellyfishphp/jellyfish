<?php

declare(strict_types = 1);

namespace Jellyfish\Uuid;

interface UuidGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
