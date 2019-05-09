<?php

namespace Jellyfish\Transfer\Definition;

use Iterator;
use Jellyfish\Finder\FinderFactoryInterface;

class DefinitionFinder implements DefinitionFinderInterface
{
    protected const IN_PATTERN = '{,vendor/*/*/}src/*/*/Transfer/';
    protected const NAME_PATTERN = '*.transfer.json';

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Jellyfish\Finder\FinderFactoryInterface
     */
    protected $finderFactory;

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
