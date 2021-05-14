<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Jellyfish\Lock\LockFacadeInterface;
use Jellyfish\Lock\LockInterface;

class LockSymfonyFacade implements LockFacadeInterface
{
    /**
     * @var \Jellyfish\LockSymfony\LockSymfonyFactory
     */
    protected LockSymfonyFactory $factory;

    /**
     * @param \Jellyfish\LockSymfony\LockSymfonyFactory $factory
     */
    public function __construct(LockSymfonyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $identifierParts
     * @param float $ttl
     *
     * @return \Jellyfish\Lock\LockInterface
     */
    public function createLock(array $identifierParts, float $ttl): LockInterface
    {
        return $this->factory->createLock($identifierParts, $ttl);
    }
}
