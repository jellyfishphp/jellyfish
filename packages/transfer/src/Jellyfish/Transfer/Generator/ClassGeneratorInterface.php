<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Generator;

use Jellyfish\Transfer\Definition\ClassDefinitionInterface;

interface ClassGeneratorInterface
{
    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return \Jellyfish\Transfer\Generator\ClassGeneratorInterface
     */
    public function generate(ClassDefinitionInterface $classDefinition): ClassGeneratorInterface;
}
