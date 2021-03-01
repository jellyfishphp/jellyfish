<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use function preg_match;

class ClassPropertyDefinition implements ClassPropertyDefinitionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $typeAlias;

    /**
     * @var string|null
     */
    protected $typeNamespace;

    /**
     * @var string|null
     */
    protected $singular;

    /**
     * @var bool
     */
    protected $isNullable;

    /**
     * @var bool|null
     */
    protected $isArray;

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
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface
     */
    public function setName(string $name): ClassPropertyDefinitionInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface
     */
    public function setType(string $type): ClassPropertyDefinitionInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTypeAlias(): ?string
    {
        return $this->typeAlias;
    }

    /**
     * @param string|null $typeAlias
     *
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface
     */
    public function setTypeAlias(?string $typeAlias): ClassPropertyDefinitionInterface
    {
        $this->typeAlias = $typeAlias;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTypeNamespace(): ?string
    {
        return $this->typeNamespace;
    }

    /**
     * @param string|null $typeNamespace
     *
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface
     */
    public function setTypeNamespace(?string $typeNamespace): ClassPropertyDefinitionInterface
    {
        $this->typeNamespace = $typeNamespace;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSingular(): ?string
    {
        return $this->singular;
    }

    /**
     * @param string|null $singular
     *
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface
     */
    public function setSingular(?string $singular): ClassPropertyDefinitionInterface
    {
        $this->singular = $singular;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * @param bool $isNullable
     *
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface
     */
    public function setIsNullable(bool $isNullable): ClassPropertyDefinitionInterface
    {
        $this->isNullable = $isNullable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrimitive(): bool
    {
        return preg_match('/^(int|float|string|bool(ean)?)$/', $this->type) === 1;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->isArray === true;
    }

    /**
     * @param bool|null $isArray
     *
     * @return \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface
     */
    public function setIsArray(?bool $isArray): ClassPropertyDefinitionInterface
    {
        $this->isArray = $isArray;

        return $this;
    }
}
