<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Jellyfish\Finder\FinderInterface;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class FinderSymfonyFactory
{
    /**
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function createFinder(): FinderInterface
    {
        return new Finder($this->createSymfonyFinder());
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    protected function createSymfonyFinder(): SymfonyFinder
    {
        return new SymfonyFinder();
    }
}
