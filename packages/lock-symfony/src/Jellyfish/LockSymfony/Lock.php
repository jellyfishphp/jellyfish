<?php

namespace Jellyfish\LockSymfony;

use Jellyfish\Lock\LockInterface;
use Symfony\Component\Lock\LockInterface as SymfonyLockInterface;

class Lock implements LockInterface
{
    /**
     * @var \Symfony\Component\Lock\LockInterface
     */
    protected $symfonyLock;

    /**
     * @param \Symfony\Component\Lock\LockInterface $symfonyLock
     */
    public function __construct(SymfonyLockInterface $symfonyLock)
    {
        $this->symfonyLock = $symfonyLock;
    }

    /**
     * @return bool
     */
    public function acquire(): bool
    {
        return $this->symfonyLock->acquire();
    }

    /**
     * @return \Jellyfish\Lock\LockInterface
     */
    public function release(): LockInterface
    {
        $this->symfonyLock->release();

        return $this;
    }
}
