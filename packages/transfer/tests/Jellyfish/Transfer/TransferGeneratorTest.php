<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;
use Jellyfish\Transfer\Definition\ClassDefinition;
use Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface;
use Jellyfish\Transfer\Generator\ClassGeneratorInterface;
use Jellyfish\Transfer\Generator\FactoryRegistryGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;

class TransferGeneratorTest extends Unit
{
    protected TransferGeneratorInterface $transferGenerator;

    protected ClassDefinitionMapLoaderInterface&MockObject $classDefinitionMapLoaderMock;

    protected MockObject&FactoryRegistryGeneratorInterface $factoryRegistryGeneratorMock;

    /**
     * @var array<\Jellyfish\Transfer\Generator\ClassGeneratorInterface&\PHPUnit\Framework\MockObject\MockObject>
     */
    protected array $classGeneratorMocks;

    /**
     * @var array<\Jellyfish\Transfer\Definition\ClassDefinition&\PHPUnit\Framework\MockObject\MockObject>
     */
    protected array $classDefinitionMapMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->classDefinitionMapMock = [
            $this->getMockBuilder(ClassDefinition::class)
            ->disableOriginalConstructor()
            ->getMock()
        ];

        $this->classDefinitionMapLoaderMock = $this->getMockBuilder(ClassDefinitionMapLoaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factoryRegistryGeneratorMock = $this->getMockBuilder(FactoryRegistryGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classGeneratorMocks = [
            $this->getMockBuilder(ClassGeneratorInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->transferGenerator = new TransferGenerator(
            $this->classDefinitionMapLoaderMock,
            $this->factoryRegistryGeneratorMock,
            $this->classGeneratorMocks
        );
    }

    /**
     * @return void
     */
    public function testGenerate(): void
    {
        $this->classDefinitionMapLoaderMock->expects($this->atLeastOnce())
            ->method('load')
            ->willReturn($this->classDefinitionMapMock);

        $this->classGeneratorMocks[0]->expects($this->atLeastOnce())
            ->method('generate')
            ->with($this->classDefinitionMapMock[0])
            ->willReturn($this->classGeneratorMocks[0]);

        $this->factoryRegistryGeneratorMock->expects($this->atLeastOnce())
            ->method('generate')
            ->with($this->classDefinitionMapMock)
            ->willReturn($this->factoryRegistryGeneratorMock);

        $this->assertEquals(
            $this->transferGenerator,
            $this->transferGenerator->generate()
        );
    }
}
