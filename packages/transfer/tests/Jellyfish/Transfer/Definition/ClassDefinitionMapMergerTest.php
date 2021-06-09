<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;

class ClassDefinitionMapMergerTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapMerger
     */
    protected ClassDefinitionMapMerger $classDefinitionMapMerger;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected array $classDefinitionMapAMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected array $classDefinitionMapBMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected array $classDefinitionPropertyMapAMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassPropertyDefinitionInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected array $classDefinitionPropertyMapBMock;

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
        $this->classDefinitionMapAMock['Product']->expects(static::atLeastOnce())
            ->method('getProperties')
            ->willReturn($this->classDefinitionPropertyMapAMock);

        $this->classDefinitionMapBMock['Product']->expects(static::atLeastOnce())
            ->method('getProperties')
            ->willReturn($this->classDefinitionPropertyMapBMock);

        $this->classDefinitionMapAMock['Product']->expects(static::atLeastOnce())
            ->method('setProperties')
            ->with($this->classDefinitionPropertyMapBMock);

        $mergedClassDefinitionMap = $this->classDefinitionMapMerger->merge(
            $this->classDefinitionMapAMock,
            $this->classDefinitionMapBMock
        );

        static::assertArrayHasKey('Product', $mergedClassDefinitionMap);
        static::assertEquals($this->classDefinitionMapAMock['Product'], $mergedClassDefinitionMap['Product']);
        static::assertArrayHasKey('Customer', $mergedClassDefinitionMap);
        static::assertEquals($this->classDefinitionMapBMock['Customer'], $mergedClassDefinitionMap['Customer']);
    }
}
