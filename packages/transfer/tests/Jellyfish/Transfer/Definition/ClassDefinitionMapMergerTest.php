<?php

declare(strict_types = 1);

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;

class ClassDefinitionMapMergerTest extends Unit
{
    /**
     * @var array<\Jellyfish\Transfer\Definition\ClassDefinitionInterface&\PHPUnit\Framework\MockObject\MockObject>
     */
    protected array $classDefinitionMapAMock;

    /**
     * @var array<\Jellyfish\Transfer\Definition\ClassDefinitionInterface&\PHPUnit\Framework\MockObject\MockObject>
     */
    protected array $classDefinitionMapBMock;

    /**
     * @var array<\Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface&\PHPUnit\Framework\MockObject\MockObject>
     */
    protected array $classDefinitionPropertyMapAMock;

    /**
     * @var array<\Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface&\PHPUnit\Framework\MockObject\MockObject>
     */
    protected array $classDefinitionPropertyMapBMock;

    protected ClassDefinitionMapMerger $classDefinitionMapMerger;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->classDefinitionMapAMock = [
            'Product' => $this->getMockBuilder(ClassDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->classDefinitionMapBMock = [
            'Product' => $this->getMockBuilder(ClassDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            'Customer' => $this->getMockBuilder(ClassDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->classDefinitionPropertyMapAMock = [
            'name' => $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->classDefinitionPropertyMapBMock = [
            'sku' => $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
            'name' => $this->getMockBuilder(ClassPropertyDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];


        $this->classDefinitionMapMerger = new ClassDefinitionMapMerger();
    }

    /**
     * @return void
     */
    public function testMerge(): void
    {
        $this->classDefinitionMapAMock['Product']->expects($this->atLeastOnce())
            ->method('getProperties')
            ->willReturn($this->classDefinitionPropertyMapAMock);

        $this->classDefinitionMapBMock['Product']->expects($this->atLeastOnce())
            ->method('getProperties')
            ->willReturn($this->classDefinitionPropertyMapBMock);

        $this->classDefinitionMapAMock['Product']->expects($this->atLeastOnce())
            ->method('setProperties')
            ->with($this->classDefinitionPropertyMapBMock);

        $mergedClassDefinitionMap = $this->classDefinitionMapMerger->merge(
            $this->classDefinitionMapAMock,
            $this->classDefinitionMapBMock,
        );

        $this->assertArrayHasKey('Product', $mergedClassDefinitionMap);
        $this->assertEquals($this->classDefinitionMapAMock['Product'], $mergedClassDefinitionMap['Product']);
        $this->assertArrayHasKey('Customer', $mergedClassDefinitionMap);
        $this->assertEquals($this->classDefinitionMapBMock['Customer'], $mergedClassDefinitionMap['Customer']);
    }
}
