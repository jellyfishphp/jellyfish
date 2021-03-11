<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Iterator;
use Jellyfish\Finder\FinderFacadeInterface;

class DefinitionFinder implements DefinitionFinderInterface
{
    protected const NAME_PATTERN = '*.transfer.json';
    protected const IN_PATTERNS = [
        'src/*/*/Transfer/',
        'packages/*/src/*/*/Transfer/',
        'vendor/*/*/src/*/*/Transfer/',
        'vendor/*/*/packages/*/src/*/*/Transfer/'
    ];

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

        return $finder->in(static::IN_PATTERNS)
            ->name(static::NAME_PATTERN)
            ->getIterator();
    }
}
