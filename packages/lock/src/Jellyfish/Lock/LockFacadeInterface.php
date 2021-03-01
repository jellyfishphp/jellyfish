<?php

declare(strict_types=1);

namespace Jellyfish\Lock;

interface LockFacadeInterface
{
    /**
     * @param array $identifierParts
     * @param float $ttl
     *
     * @return \Jellyfish\Lock\LockInterface
     */
    public function createLock(array $identifierParts, float $ttl): LockInterface;
}
