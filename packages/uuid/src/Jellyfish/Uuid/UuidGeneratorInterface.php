<?php

namespace Jellyfish\Uuid;

interface UuidGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
