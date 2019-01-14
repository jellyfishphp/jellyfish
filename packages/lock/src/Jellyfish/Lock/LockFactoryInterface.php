<?php

namespace Jellyfish\Lock;

interface LockFactoryInterface
{
    /**
     * @param string $identifier
     * @param float $ttl
     *
     * @return \Jellyfish\Lock\LockInterface
     */
    public function create(string $identifier, float $ttl): LockInterface;
}
