<?php

namespace Jellyfish\Transfer\Definition;

interface ClassDefinitionInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface
     */
    public function setName(string $name): ClassDefinitionInterface;

    /**
     * @return string|null
     */
    public function getNamespace(): ?string;

    /**
     * @param string|null $namespace
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface
     */
    public function setNamespace(?string $namespace): ClassDefinitionInterface;

    /**
     * @return array
     */
    public function getUseStatements(): array;

    /**
     * @return string
     */
    public function getNamespaceStatement(): string;

    /**
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface[]
     */
    public function getProperties(): array;

    /**
     * @param \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface[] $properties
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface
     */
    public function setProperties(array $properties): ClassDefinitionInterface;
}
