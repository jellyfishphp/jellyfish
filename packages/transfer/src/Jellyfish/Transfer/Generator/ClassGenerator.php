<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Generator;

use Jellyfish\Transfer\Definition\ClassDefinitionInterface;

/**
 * @see \Jellyfish\Transfer\Generator\ClassGeneratorTest
 */
class ClassGenerator extends AbstractClassGenerator
{
    protected const TEMPLATE_NAME = 'class.twig';

    /**
     * @param \Jellyfish\Transfer\Definition\ClassDefinitionInterface $classDefinition
     *
     * @return string
     */
    protected function getFile(ClassDefinitionInterface $classDefinition): string
    {
        return $classDefinition->getName() . static::FILE_EXTENSION;
    }

    /**
     * @return string
     */
    protected function getTemplateName(): string
    {
        return static::TEMPLATE_NAME;
    }
}
