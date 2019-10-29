<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Generator;

interface FactoryRegistryGeneratorInterface
{
    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface[] $classDefinitionMap
     *
     * @return \Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface
     */
    public function generate(array $classDefinitionMap): FactoryRegistryGeneratorInterface;
}
