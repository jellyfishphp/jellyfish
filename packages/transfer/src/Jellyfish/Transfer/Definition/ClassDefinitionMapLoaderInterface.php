<?php

namespace Jellyfish\Transfer\Definition;

interface ClassDefinitionMapLoaderInterface
{
    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]
     */
    public function load(): array;
}
