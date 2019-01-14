<?php

namespace Jellyfish\Lock;

trait LockTrait
{
    /**
     * @var \Jellyfish\Lock\LockFactoryInterface
     */
    private $lockFactory;

    /**
     * @var \Jellyfish\Lock\LockInterface
     */
    private $lock;

    /**
     * @param string $identifier
     * @param float $ttl
     *
     * @return bool
     */
    private function acquire(string $identifier, float $ttl = 360.0): bool
    {
        if ($this->lockFactory === null) {
            return true;
        }

        $this->lock = $this->lockFactory->create($identifier, $ttl);

        if (!$this->lock->acquire()) {
            $this->lock = null;
            return false;
        }

        return true;
    }

    /**
     * @return \Jellyfish\Lock\LockTrait
     */
    private function release(): self
    {
        if ($this->lock === null) {
            return $this;
        }

        $this->lock->release();
        $this->lock = null;

        return $this;
    }

    /**
     * @param array $values
     *
     * @return string
     */
    private function createIdentifier(array $values): string
    {
        $value = implode(' ', $values);

        return sha1($value);
    }
}
