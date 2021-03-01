<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Iterator;
use Jellyfish\Finder\FinderFacadeInterface;

class DefinitionFinder implements DefinitionFinderInterface
{
    protected const IN_PATTERN = '{,vendor/*/*/}src/*/*/Transfer/';
    protected const NAME_PATTERN = '*.transfer.json';

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Jellyfish\Finder\FinderFacadeInterface
     */
    protected $finderFacade;

    /**
     * @param \Jellyfish\Finder\FinderFacadeInterface $finderFacade
     * @param string $rootDir
     */
    public function __construct(
        FinderFacadeInterface $finderFacade,
        string $rootDir
    ) {
        $this->rootDir = $rootDir;
        $this->finderFacade = $finderFacade;
    }

    /**
     * @return \Iterator
     */
    public function find(): Iterator
    {
        $finder = $this->finderFacade->createFinder();

        return $finder->in(static::IN_PATTERN)
            ->name(static::NAME_PATTERN)
            ->getIterator();
    }
}
