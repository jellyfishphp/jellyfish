<?php

namespace Jellyfish\LockSymfony;

use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockInterface;
use Symfony\Component\Lock\Factory as SymfonyLockFactory;

class LockFactory implements LockFactoryInterface
{
    /**
     * @var \Symfony\Component\Lock\Factory
     */
    protected $symfonyLockFactory;

    /**
     * @param \Symfony\Component\Lock\Factory $symfonyLockFactory
     */
    public function __construct(SymfonyLockFactory $symfonyLockFactory)
    {
        $this->symfonyLockFactory = $symfonyLockFactory;
    }

    /**
     * @param string $identifier
     * @param float $ttl
     *
     * @return \Jellyfish\Lock\LockInterface
     */
    public function create(string $identifier, float $ttl): LockInterface
    {
        $symfonyLock = $this->symfonyLockFactory->createLock($identifier, $ttl, false);

        return new Lock($symfonyLock);
    }
}
