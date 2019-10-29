<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

interface ClassDefinitionMapMergerInterface
{
    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $classDefinitionMapA
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $classDefinitionMapB
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]
     */
    public function merge(array $classDefinitionMapA, array $classDefinitionMapB): array;
}
