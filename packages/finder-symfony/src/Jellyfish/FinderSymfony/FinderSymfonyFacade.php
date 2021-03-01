<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Jellyfish\Finder\FinderFacadeInterface;
use Jellyfish\Finder\FinderInterface;

class FinderSymfonyFacade implements FinderFacadeInterface
{
    /**
     * @var \Jellyfish\FinderSymfony\FinderSymfonyFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\FinderSymfony\FinderSymfonyFactory $factory
     */
    public function __construct(FinderSymfonyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function createFinder(): FinderInterface
    {
        return $this->factory->createFinder();
    }
}
