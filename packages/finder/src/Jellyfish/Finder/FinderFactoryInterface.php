<?php

namespace Jellyfish\Finder;

interface FinderFactoryInterface
{
    /**
     * @return \Jellyfish\Finder\FinderInterface
     */
    public function create(): FinderInterface;
}
