<?php

declare(strict_types = 1);

namespace Jellyfish\Lock;

interface LockFactoryInterface
{
    /**
     * @param array $identifierParts
     * @param float $ttl
     *
     * @return \Jellyfish\Lock\LockInterface
     */
    public function create(array $identifierParts, float $ttl): LockInterface;
}
