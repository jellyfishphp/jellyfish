<?php

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
