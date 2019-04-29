<?php

namespace Jellyfish\Transfer\ClassGenerator;

use Jellyfish\Transfer\Definition\ClassDefinition;
use Jellyfish\Transfer\Definition\ClassDefinitionInterface;

class FactoryClassGenerator extends AbstractClassGenerator
{
    protected const TEMPLATE_NAME = 'factory-class.twig';

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return string
     */
    protected function getFile(ClassDefinitionInterface $classDefinition): string
    {
        return $classDefinition->getName() . ClassDefinition::FACTORY_NAME_SUFFIX . static::FILE_EXTENSION;
    }

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return static::TEMPLATE_NAME;
    }
}
