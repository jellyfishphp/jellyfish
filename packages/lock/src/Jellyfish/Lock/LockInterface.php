<?php

declare(strict_types=1);

namespace Jellyfish\Lock;

interface LockInterface
{
    /**
     * @return bool
     */
    public function acquire(): bool;

    /**
     * @return \Jellyfish\Lock\LockInterface
     */
    public function release(): LockInterface;
}
