<?php

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;
use Jellyfish\Serializer\SerializerInterface;

class ClassDefinitionMapMapperTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapMapper
     */
    protected $classDefinitionMapMapper;

    /**
     * @var \Jellyfish\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerMock;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinition[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected $classDefinitionMocks;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassPropertyDefinition[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected $classPropertyDefinitionMocks;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classDefinitionMocks = new \ArrayObject([
            $this->getMockBuilder(ClassDefinition::class)
                ->disableOriginalConstructor()
                ->getMock()
        ]);

        $this->classPropertyDefinitionMocks = [
            $this->getMockBuilder(ClassPropertyDefinition::class)
                ->disableOriginalConstructor()
                ->getMock()
        ];

        $this->classDefinitionMapMapper = new ClassDefinitionMapMapper($this->serializerMock);
    }

    /**
     * @return void
     */
    public function testFrom(): void
    {
        $json = '[{...}]';

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('deserialize')
            ->with($json, ClassDefinition::class . '[]', 'json')
            ->willReturn($this->classDefinitionMocks);

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('Product');

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn(null);

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getProperties')
            ->willReturn($this->classPropertyDefinitionMocks);

        $this->classPropertyDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('sku');

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('setProperties')
            ->with(['sku' => $this->classPropertyDefinitionMocks[0]]);

        $classDefinitionMap = $this->classDefinitionMapMapper->from($json);

        $this->assertEquals(['Product' => $this->classDefinitionMocks[0]], $classDefinitionMap);
    }

    /**
     * @return void
     */
    public function testFromWithNamespace(): void
    {
        $json = '[{...}]';

        $this->serializerMock->expects($this->atLeastOnce())
            ->method('deserialize')
            ->with($json, ClassDefinition::class . '[]', 'json')
            ->willReturn($this->classDefinitionMocks);

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('Product');

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getNamespace')
            ->willReturn('Catalog');

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getProperties')
            ->willReturn($this->classPropertyDefinitionMocks);

        $this->classPropertyDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('sku');

        $this->classDefinitionMocks[0]->expects($this->atLeastOnce())
            ->method('setProperties')
            ->with(['sku' => $this->classPropertyDefinitionMocks[0]]);

        $classDefinitionMap = $this->classDefinitionMapMapper->from($json);

        $this->assertEquals(['Catalog\\Product' => $this->classDefinitionMocks[0]], $classDefinitionMap);
    }
}
