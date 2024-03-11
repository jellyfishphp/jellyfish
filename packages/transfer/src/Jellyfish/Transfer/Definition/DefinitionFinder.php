<?php

declare(strict_types = 1);

namespace Jellyfish\Transfer\Definition;

use Iterator;
use Jellyfish\Finder\FinderFactoryInterface;

/**
 * @see \Jellyfish\Transfer\Definition\DefinitionFinderTest
 */
class DefinitionFinder implements DefinitionFinderInterface
{
    protected const IN_PATTERN = '{,vendor/*/*/}src/*/*/Transfer/';

    protected const NAME_PATTERN = '*.transfer.json';

    protected string $rootDir;

    protected FinderFactoryInterface $finderFactory;

    /**
     * @param \Jellyfish\Finder\FinderFactoryInterface $finderFactory
     * @param string $rootDir
     */
    public function __construct(
        FinderFactoryInterface $finderFactory,
        string $rootDir
    ) {
        $this->rootDir = $rootDir;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @return \Iterator
     */
    public function find(): Iterator
    {
        $finder = $this->finderFactory->create();

        return $finder->in(static::IN_PATTERN)
            ->name(static::NAME_PATTERN)
            ->getIterator();
    }
}
