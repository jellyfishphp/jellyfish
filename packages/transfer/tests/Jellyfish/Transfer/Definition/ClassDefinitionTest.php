<?php

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;

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
