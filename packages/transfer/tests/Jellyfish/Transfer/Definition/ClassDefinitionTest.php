<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;
use RuntimeException;

use function sprintf;

class ClassDefinitionTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinition
     */
    protected ClassDefinition $classDefinition;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->classDefinition = new ClassDefinition();
    }

    /**
     * @return void
     */
    public function testGetIdWithoutNamespace(): void
    {
        $this->classDefinition->setName('Product');

        static::assertEquals('generated_transfer_product', $this->classDefinition->getId());
    }

    /**
     * @return void
     */
    public function testGetIdWithInvalidPattern(): void
    {
        $extendedClassDefinition = new class () extends ClassDefinition {
            protected const PATTERN_ID = '';
        };

        $extendedClassDefinition->setName('Product')
            ->setNamespace('Catalog');

        try {
            $extendedClassDefinition->getId();
            static::fail();
        } catch (RuntimeException $e) {
        }
    }

    /**
     * @return void
     */
    public function testGetId(): void
    {
        $this->classDefinition->setName('Product')
            ->setNamespace('Catalog');

        static::assertEquals('generated_transfer_catalog_product', $this->classDefinition->getId());
    }

    /**
     * @return void
     */
    public function testSetAndGetName(): void
    {
        $name = 'Product';
        static::assertEquals($this->classDefinition, $this->classDefinition->setName($name));
        static::assertEquals($name, $this->classDefinition->getName());
    }

    /**
     * @return void
     */
    public function testSetAndGetNamespace(): void
    {
        $namespace = 'Catalog';
        static::assertEquals($this->classDefinition, $this->classDefinition->setNamespace($namespace));
        static::assertEquals($namespace, $this->classDefinition->getNamespace());
    }

    /**
     * @return void
     */
    public function testGetNamespaceStatement(): void
    {
        $namespace = 'Lorem';
        $expectedNamespaceStatement = sprintf('namespace %s\\%s;', ClassDefinition::NAMESPACE_PREFIX, $namespace);

        static::assertEquals($this->classDefinition, $this->classDefinition->setNamespace($namespace));
        static::assertEquals($expectedNamespaceStatement, $this->classDefinition->getNamespaceStatement());
    }

    /**
     * @return void
     */
    public function testGetNamespaceStatementWithNullableNamespace(): void
    {
        $expectedNamespaceStatement = sprintf('namespace %s;', ClassDefinition::NAMESPACE_PREFIX);
        static::assertEquals($expectedNamespaceStatement, $this->classDefinition->getNamespaceStatement());
    }

    /**
     * @return void
     */
    public function testUseStatements(): void
    {
        $propertyMocks = [
            $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        static::assertEquals($this->classDefinition, $this->classDefinition->setNamespace('Lorem'));
        static::assertEquals($this->classDefinition, $this->classDefinition->setProperties($propertyMocks));

        $propertyMocks[0]->expects(static::atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(true);

        $propertyMocks[1]->expects(static::atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(false);

        $propertyMocks[1]->expects(static::atLeastOnce())
            ->method('getTypeNamespace')
            ->willReturn('Ipsum');

        $propertyMocks[1]->expects(static::atLeastOnce())
            ->method('getTypeAlias')
            ->willReturn(null);

        $propertyMocks[1]->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn('A');

        $propertyMocks[2]->expects(static::atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(false);

        $propertyMocks[2]->expects(static::atLeastOnce())
            ->method('getTypeNamespace')
            ->willReturn('Ipsum');

        $propertyMocks[2]->expects(static::atLeastOnce())
            ->method('getTypeAlias')
            ->willReturn(null);

        $propertyMocks[2]->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn('A');

        $propertyMocks[3]->expects(static::atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(false);

        $propertyMocks[3]->expects(static::atLeastOnce())
            ->method('getTypeNamespace')
            ->willReturn('Lorem');

        $propertyMocks[3]->expects(static::atLeastOnce())
            ->method('getTypeAlias')
            ->willReturn('LoremA');

        $propertyMocks[3]->expects(static::atLeastOnce())
            ->method('getType')
            ->willReturn('A');

        static::assertCount(2, $this->classDefinition->getUseStatements());
    }

    /**
     * @return void
     */
    public function testSetAndGetProperties(): void
    {
        $propertyMocks = [
            $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        static::assertEquals($this->classDefinition, $this->classDefinition->setProperties($propertyMocks));

        $properties = $this->classDefinition->getProperties();

        static::assertCount(count($propertyMocks), $properties);
        static::assertEquals($propertyMocks, $properties);
    }
}
