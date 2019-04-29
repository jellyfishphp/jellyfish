<?php

namespace Jellyfish\Transfer\ClassGenerator;

use Jellyfish\Transfer\Definition\ClassDefinitionInterface;

interface ClassGeneratorInterface
{
    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return \Jellyfish\Transfer\ClassGenerator\ClassGeneratorInterface
     */
    public function generate(ClassDefinitionInterface $classDefinition): ClassGeneratorInterface;
}
