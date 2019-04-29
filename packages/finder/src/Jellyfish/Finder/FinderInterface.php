<?php

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
     * @return \Iterator
     */
    public function getIterator(): Iterator;
}
