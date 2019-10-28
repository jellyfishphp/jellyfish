<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;
use RuntimeException;

class ClassDefinitionTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinition
     */
    protected $classDefinition;

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

        $this->assertEquals('generated_transfer_product', $this->classDefinition->getId());
    }

    /**
     * @return void
     */
    public function testGetIdWithInvalidPattern(): void
    {
        $extendedClassDefinition = new class() extends ClassDefinition
        {
            protected const PATTERN_ID = '';
        };

        $extendedClassDefinition->setName('Product')
            ->setNamespace('Catalog');

        try {
            $extendedClassDefinition->getId();
            $this->fail();
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

        $this->assertEquals('generated_transfer_catalog_product', $this->classDefinition->getId());
    }

    /**
     * @return void
     */
    public function testSetAndGetName(): void
    {
        $name = 'Product';
        $this->assertEquals($this->classDefinition, $this->classDefinition->setName($name));
        $this->assertEquals($name, $this->classDefinition->getName());
    }

    /**
     * @return void
     */
    public function testSetAndGetNamespace(): void
    {
        $namespace = 'Catalog';
        $this->assertEquals($this->classDefinition, $this->classDefinition->setNamespace($namespace));
        $this->assertEquals($namespace, $this->classDefinition->getNamespace());
    }

    /**
     * @return void
     */
    public function testGetNamespaceStatement(): void
    {
        $namespace = 'Lorem';
        $expectedNamespaceStatement = \sprintf('namespace %s\\%s;', ClassDefinition::NAMESPACE_PREFIX, $namespace);

        $this->assertEquals($this->classDefinition, $this->classDefinition->setNamespace($namespace));
        $this->assertEquals($expectedNamespaceStatement, $this->classDefinition->getNamespaceStatement());
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

        $this->assertEquals($this->classDefinition, $this->classDefinition->setNamespace('Lorem'));
        $this->assertEquals($this->classDefinition, $this->classDefinition->setProperties($propertyMocks));

        $propertyMocks[0]->expects($this->atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(true);

        $propertyMocks[1]->expects($this->atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(false);

        $propertyMocks[1]->expects($this->atLeastOnce())
            ->method('getTypeNamespace')
            ->willReturn('Ipsum');

        $propertyMocks[1]->expects($this->atLeastOnce())
            ->method('getTypeAlias')
            ->willReturn(null);

        $propertyMocks[1]->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn('A');

        $propertyMocks[2]->expects($this->atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(false);

        $propertyMocks[2]->expects($this->atLeastOnce())
            ->method('getTypeNamespace')
            ->willReturn('Ipsum');

        $propertyMocks[2]->expects($this->atLeastOnce())
            ->method('getTypeAlias')
            ->willReturn(null);

        $propertyMocks[2]->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn('A');

        $propertyMocks[3]->expects($this->atLeastOnce())
            ->method('isPrimitive')
            ->willReturn(false);

        $propertyMocks[3]->expects($this->atLeastOnce())
            ->method('getTypeNamespace')
            ->willReturn('Lorem');

        $propertyMocks[3]->expects($this->atLeastOnce())
            ->method('getTypeAlias')
            ->willReturn('LoremA');

        $propertyMocks[3]->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn('A');

        $this->assertCount(2, $this->classDefinition->getUseStatements());
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

        $this->assertEquals($this->classDefinition, $this->classDefinition->setProperties($propertyMocks));

        $properties = $this->classDefinition->getProperties();

        $this->assertCount(count($propertyMocks), $properties);
        $this->assertEquals($propertyMocks, $properties);
    }
}
