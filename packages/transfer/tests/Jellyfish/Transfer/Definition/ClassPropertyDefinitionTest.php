<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;

class ClassPropertyDefinitionTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassPropertyDefinition
     */
    protected $classPropertyDefinition;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->classPropertyDefinition = new ClassPropertyDefinition();
    }

    /**
     * @return void
     */
    public function testSetAndGetName(): void
    {
        $name = 'sku';
        $this->classPropertyDefinition->setName($name);
        static::assertEquals($name, $this->classPropertyDefinition->getName());
    }

    /**
     * @return void
     */
    public function testSetAndGetType(): void
    {
        $type = 'string';
        $this->classPropertyDefinition->setType($type);
        static::assertEquals($type, $this->classPropertyDefinition->getType());
    }

    /**
     * @return void
     */
    public function testSetAndIsArray(): void
    {
        $this->classPropertyDefinition->setIsArray(null);
        static::assertFalse($this->classPropertyDefinition->isArray());
    }

    /**
     * @return void
     */
    public function testIsPrimitiveWithObjectType(): void
    {
        $this->classPropertyDefinition->setType('Customer');
        static::assertFalse($this->classPropertyDefinition->isPrimitive());
    }

    /**
     * @return void
     */
    public function testIsPrimitiveWithStringType(): void
    {
        $this->classPropertyDefinition->setType('string');
        static::assertTrue($this->classPropertyDefinition->isPrimitive());
    }

    /**
     * @return void
     */
    public function testSetAndGetTypeAlias(): void
    {
        $typeAlias = 'Alias';
        $this->classPropertyDefinition->setTypeAlias($typeAlias);
        static::assertEquals($typeAlias, $this->classPropertyDefinition->getTypeAlias());
    }

    /**
     * @return void
     */
    public function testSetAndGetTypeNamespace(): void
    {
        $typeNamespace = 'Catalog';
        $this->classPropertyDefinition->setTypeNamespace($typeNamespace);
        static::assertEquals($typeNamespace, $this->classPropertyDefinition->getTypeNamespace());
    }

    /**
     * @return void
     */
    public function testSetAndGetSingular(): void
    {
        $singular = 'product';
        $this->classPropertyDefinition->setSingular($singular);
        static::assertEquals($singular, $this->classPropertyDefinition->getSingular());
    }

    /**
     * @return void
     */
    public function testSetAndIsNullable(): void
    {
        $this->classPropertyDefinition->setIsNullable(true);
        static::assertTrue($this->classPropertyDefinition->isNullable());
    }
}
