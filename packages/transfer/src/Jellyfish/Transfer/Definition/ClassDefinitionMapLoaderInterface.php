<?php

namespace Jellyfish\Transfer\Definition;

interface ClassDefinitionMapLoaderInterface
{
    /**
     * @return \Jellyfish\Transfer\Definition\ClassDefinition[]
     */
    public function load(): array;
}
