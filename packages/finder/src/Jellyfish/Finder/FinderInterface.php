<?php

declare(strict_types=1);

namespace Jellyfish\Finder;

use Iterator;
use IteratorAggregate;

interface FinderInterface extends IteratorAggregate
{
    /**
     * @param string $pattern
     *
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function in(string $pattern): FinderInterface;

    /**
     * @param string $pattern
     *
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function name(string $pattern): FinderInterface;

    /**
     * @param int $level
     *
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function depth(int $level): FinderInterface;

    /**
     * @return \Iterator
     */
    public function getIterator(): Iterator;
}
