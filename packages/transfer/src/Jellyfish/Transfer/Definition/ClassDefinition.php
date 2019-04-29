<?php

namespace Jellyfish\Transfer\Definition;

class ClassDefinition implements ClassDefinitionInterface
{
    public const NAMESPACE_PREFIX = 'Generated\\Transfer';
    public const NAMESPACE_SEPARATOR = '\\';
    public const FACTORY_NAME_SUFFIX = 'TransferFactory';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $namespace;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface[]
     */
    protected $properties;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface
     */
    public function setName(string $name): ClassDefinitionInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @param string|null $namespace
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface
     */
    public function setNamespace(?string $namespace): ClassDefinitionInterface
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface[] $properties
     *
     * @return \Jellyfish\Transfer\Definition\ClassDefinitionInterface
     */
    public function setProperties(array $properties): ClassDefinitionInterface
    {
        $this->properties = $properties;

        return $this;
    }
}
