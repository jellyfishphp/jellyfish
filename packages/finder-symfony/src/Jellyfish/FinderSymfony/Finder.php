<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Iterator;
use Jellyfish\Finder\FinderInterface;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder implements FinderInterface
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $symfonyFinder;

    /**
     * @param \Symfony\Component\Finder\Finder $symfonyFinder
     */
    public function __construct(SymfonyFinder $symfonyFinder)
    {
        $this->symfonyFinder = $symfonyFinder;
    }

    /**
     * @param string[] $directories
     *
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function in(array $directories): FinderInterface
    {
        $this->symfonyFinder->in($directories);

        return $this;
    }

    /**
     * @param string $pattern
     *
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function name(string $pattern): FinderInterface
    {
        $this->symfonyFinder->name($pattern);

        return $this;
    }

    /**
     * @param int $level
     *
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function depth(int $level): FinderInterface
    {
        $this->symfonyFinder->depth($level);

        return $this;
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): Iterator
    {
        return $this->symfonyFinder->getIterator();
    }
}
