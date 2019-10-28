<?php

declare(strict_types=1);

namespace Jellyfish\Finder;

interface FinderFactoryInterface
{
    /**
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function create(): FinderInterface;
}
