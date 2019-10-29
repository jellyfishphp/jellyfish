<?php

declare(strict_types=1);

namespace Jellyfish\Lock;

trait LockTrait
{
    /**
     * @var \Jellyfish\Lock\LockFactoryInterface
     */
    private $lockFactory;

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
        if ($this->lockFactory === null) {
            return true;
        }

        $this->lock = $this->lockFactory->create($identifierParts, $ttl);

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
