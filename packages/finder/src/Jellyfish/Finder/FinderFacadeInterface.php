<?php

declare(strict_types=1);

namespace Jellyfish\Finder;

interface FinderFacadeInterface
{
    /**
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function createFinder(): FinderInterface;
}
