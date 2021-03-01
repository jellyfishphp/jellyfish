<?php

declare(strict_types=1);

namespace Jellyfish\Lock;

trait LockTrait
{
    /**
     * @var \Jellyfish\Lock\LockFacadeInterface
     */
    private $lockFacade;

    /**
     * @var \Jellyfish\Lock\LockInterface|null
     */
    private $lock;

    /**
     * @param array $identifierParts
     * @param float $ttl
     *
     * @return bool
     */
    private function acquire(array $identifierParts, float $ttl = 360.0): bool
    {
        if ($this->lockFacade === null) {
            return true;
        }

        $this->lock = $this->lockFacade->createLock($identifierParts, $ttl);

        if (!$this->lock->acquire()) {
            $this->lock = null;
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    private function release(): void
    {
        if ($this->lock === null) {
            return;
        }

        $this->lock->release();
        $this->lock = null;
    }
}
